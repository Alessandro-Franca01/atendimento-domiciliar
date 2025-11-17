<?php

namespace App\Services;

use App\Models\Atendimento;
use App\Models\Fatura;
use App\Models\FaturaItem;
use App\Models\Pagamento;
use App\Models\Sessao;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function pagarSessao(Sessao $sessao, array $data): Pagamento
    {
        return DB::transaction(function () use ($sessao, $data) {
            if ($sessao->valor_total === null) {
                throw new \InvalidArgumentException('Sessão não possui valor_total definido.');
            }

            $valor = (float) $data['valor'];
            $novoPago = (float) $sessao->valor_pago + $valor;
            if ($novoPago - (float) $sessao->valor_total > 0.00001) {
                throw new \InvalidArgumentException('Valor do pagamento excede o total da sessão.');
            }

            if (!empty($data['fatura_id'])) {
                $this->validarLimiteFaturaPagamento((int) $data['fatura_id'], (float) $data['valor']);
            }

            $pagamento = Pagamento::create([
                'sessao_id' => $sessao->id,
                'paciente_id' => $sessao->paciente_id,
                'profissional_id' => $sessao->profissional_id,
                'fatura_id' => $data['fatura_id'] ?? null,
                'metodo_pagamento' => $data['metodo_pagamento'],
                'valor' => $valor,
                'data_pagamento' => Carbon::parse($data['data_pagamento'] ?? Carbon::now())->toDateString(),
                'status' => $data['status'] ?? 'pago',
                'observacoes' => $data['observacoes'] ?? null,
            ]);

            $sessao->update(['valor_pago' => $novoPago]);

            if (abs($sessao->valor_total - $novoPago) < 0.00001) {
                $this->marcarAtendimentosDaSessaoComoPagoViaSessao($sessao);
            }

            return $pagamento;
        });
    }

    public function pagarAtendimento(Atendimento $atendimento, array $data): Pagamento
    {
        return DB::transaction(function () use ($atendimento, $data) {
            $dataPagamento = Carbon::parse($data['data_pagamento'] ?? Carbon::now())->toDateString();

            $duplicado = Pagamento::where('atendimento_id', $atendimento->id)
                ->whereDate('data_pagamento', $dataPagamento)
                ->exists();
            if ($duplicado) {
                throw new \InvalidArgumentException('Pagamento duplicado para o mesmo atendimento no mesmo dia.');
            }

            if (!empty($data['fatura_id'])) {
                $this->validarLimiteFaturaPagamento((int) $data['fatura_id'], (float) $data['valor']);
            }

            $pagamento = Pagamento::create([
                'atendimento_id' => $atendimento->id,
                'paciente_id' => $atendimento->paciente_id,
                'profissional_id' => $atendimento->profissional_id,
                'fatura_id' => $data['fatura_id'] ?? null,
                'metodo_pagamento' => $data['metodo_pagamento'],
                'valor' => (float) $data['valor'],
                'data_pagamento' => $dataPagamento,
                'status' => $data['status'] ?? 'pago',
                'observacoes' => $data['observacoes'] ?? null,
            ]);

            $atendimento->update(['status_pagamento' => 'pago']);

            return $pagamento;
        });
    }

    public function validarLimiteFaturaPagamento(int $faturaId, float $novoValor): void
    {
        $fatura = Fatura::findOrFail($faturaId);
        $totalPagamentos = $fatura->pagamentos()->where('status', '!=', 'estornado')->sum('valor');
        if (($totalPagamentos + $novoValor) - (float) $fatura->valor_total > 0.00001) {
            throw new \InvalidArgumentException('Valor dos pagamentos excede o total da fatura.');
        }
    }

    public function estornarPagamento(Pagamento $pagamento): void
    {
        DB::transaction(function () use ($pagamento) {
            if ($pagamento->status === 'estornado') {
                return;
            }

            $pagamento->update(['status' => 'estornado']);

            if ($pagamento->sessao_id) {
                $sessao = $pagamento->sessao;
                $novoPago = max(0.0, (float) $sessao->valor_pago - (float) $pagamento->valor);
                $sessao->update(['valor_pago' => $novoPago]);

                if ($sessao->saldo_pagamento > 0) {
                    $this->marcarAtendimentosDaSessaoComoPendentes($sessao);
                }
            }

            if ($pagamento->atendimento_id) {
                $atendimento = $pagamento->atendimento;
                $atendimento->update(['status_pagamento' => 'pendente']);
            }
        });
    }

    public function gerarFaturaMensal(int $pacienteId, Carbon $mesRef): Fatura
    {
        return DB::transaction(function () use ($pacienteId, $mesRef) {
            $inicio = $mesRef->copy()->startOfMonth();
            $fim = $mesRef->copy()->endOfMonth();

            $atendimentos = Atendimento::where('paciente_id', $pacienteId)
                ->whereBetween('data_realizacao', [$inicio, $fim])
                ->get();

            $valorTotal = 0.0;

            $fatura = Fatura::create([
                'paciente_id' => $pacienteId,
                'valor_total' => 0,
                'data_emissao' => Carbon::now()->toDateString(),
                'data_vencimento' => $fim->toDateString(),
                'status' => 'aberta',
                'tipo' => 'mensalidade',
            ]);

            foreach ($atendimentos as $atendimento) {
                $valorItem = (float) ($atendimento->valor ?? 0); // se existir campo futuro
                if ($valorItem <= 0) {
                    $valorItem = (float) 0;
                }
                $valorTotal += $valorItem;
                FaturaItem::create([
                    'fatura_id' => $fatura->id,
                    'descricao' => 'Atendimento em ' . Carbon::parse($atendimento->data_realizacao)->format('d/m/Y'),
                    'quantidade' => 1,
                    'valor_unitario' => $valorItem,
                    'valor_total' => $valorItem,
                    'atendimento_id' => $atendimento->id,
                    'sessao_id' => null,
                ]);
            }

            $fatura->update(['valor_total' => $valorTotal]);

            return $fatura;
        });
    }

    private function marcarAtendimentosDaSessaoComoPagoViaSessao(Sessao $sessao): void
    {
        $agendamentoIds = $sessao->agendamentos()->pluck('id');
        Atendimento::whereIn('agendamento_id', $agendamentoIds)
            ->update(['status_pagamento' => 'pago_via_sessao']);
    }

    private function marcarAtendimentosDaSessaoComoPendentes(Sessao $sessao): void
    {
        $agendamentoIds = $sessao->agendamentos()->pluck('id');
        Atendimento::whereIn('agendamento_id', $agendamentoIds)
            ->update(['status_pagamento' => 'pendente']);
    }
}