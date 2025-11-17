<?php

namespace App\Services;

use App\Models\Agendamento;
use App\Models\Sessao;
use App\Models\SessaoHorario;
use Carbon\Carbon;

class AgendamentoService
{
    /**
     * Gera agendamentos automáticos baseados nos horários fixos das sessões
     */
    public function gerarAgendamentosAutomaticos($dias = 30)
    {
        $agendamentosCriados = 0;
        $dataInicio = Carbon::now()->startOfDay();
        $dataFim = Carbon::now()->addDays($dias)->endOfDay();

        // Buscar todas as sessões ativas com horários fixos
        $sessoes = Sessao::where('status', 'ativo')
            ->whereHas('sessaoHorarios', function ($query) {
                $query->where('ativo', true);
            })
            ->with(['sessaoHorarios' => function ($query) {
                $query->where('ativo', true);
            }])
            ->get();

        foreach ($sessoes as $sessao) {
            foreach ($sessao->sessaoHorarios as $horarioFixo) {
                $agendamentosCriados += $this->criarAgendamentosParaHorarioFixo(
                    $sessao,
                    $horarioFixo,
                    $dataInicio,
                    $dataFim
                );
            }
        }

        return $agendamentosCriados;
    }

    /**
     * Cria agendamentos para um horário fixo específico dentro de um período
     */
    private function criarAgendamentosParaHorarioFixo(Sessao $sessao, SessaoHorario $horarioFixo, Carbon $dataInicio, Carbon $dataFim)
    {
        $agendamentosCriados = 0;
        $dataAtual = $dataInicio->copy();

        while ($dataAtual <= $dataFim) {
            // Verificar se o dia da semana corresponde ao horário fixo
            if ($dataAtual->dayOfWeek + 1 == $horarioFixo->dia_da_semana) {
                // Criar agendamento se não existir
                if (!$this->agendamentoExiste($sessao, $horarioFixo, $dataAtual)) {
                    $this->criarAgendamento($sessao, $horarioFixo, $dataAtual);
                    $agendamentosCriados++;
                }
            }
            
            $dataAtual->addDay();
        }

        return $agendamentosCriados;
    }

    /**
     * Verifica se já existe um agendamento para a sessão, horário e data específicos
     */
    private function agendamentoExiste(Sessao $sessao, SessaoHorario $horarioFixo, Carbon $data)
    {
        $dataHoraInicio = $data->copy()->setTimeFromTimeString($horarioFixo->hora);
        $dataHoraFim = $dataHoraInicio->copy()->addMinutes($horarioFixo->duracao_minutos);

        return Agendamento::where('sessao_id', $sessao->id)
            ->where('sessao_horario_id', $horarioFixo->id)
            ->where('data_hora_inicio', $dataHoraInicio)
            ->where('data_hora_fim', $dataHoraFim)
            ->exists();
    }

    /**
     * Cria um novo agendamento
     */
    private function criarAgendamento(Sessao $sessao, SessaoHorario $horarioFixo, Carbon $data)
    {
        $dataHoraInicio = $data->copy()->setTimeFromTimeString($horarioFixo->hora);
        $dataHoraFim = $dataHoraInicio->copy()->addMinutes($horarioFixo->duracao_minutos);

        // Verificar se o profissional está disponível
        if (!$this->profissionalDisponivel($sessao->profissional_id, $dataHoraInicio, $dataHoraFim)) {
            return null;
        }

        // Verificar se a sessão ainda tem sessões restantes
        if ($sessao->isCompleta()) {
            return null;
        }

        return Agendamento::create([
            'sessao_id' => $sessao->id,
            'sessao_horario_id' => $horarioFixo->id,
            'paciente_id' => $sessao->paciente_id,
            'endereco_id' => $horarioFixo->endereco_id,
            'profissional_id' => $sessao->profissional_id,
            'data_hora_inicio' => $dataHoraInicio,
            'data_hora_fim' => $dataHoraFim,
            'status' => 'agendado',
            'observacoes' => 'Agendamento automático',
        ]);
    }

    /**
     * Verifica se o profissional está disponível em um horário específico
     */
    private function profissionalDisponivel($profissionalId, Carbon $dataHoraInicio, Carbon $dataHoraFim)
    {
        return !Agendamento::where('profissional_id', $profissionalId)
            ->where('status', '!=', 'cancelado')
            ->where(function ($query) use ($dataHoraInicio, $dataHoraFim) {
                $query->whereBetween('data_hora_inicio', [$dataHoraInicio, $dataHoraFim])
                    ->orWhereBetween('data_hora_fim', [$dataHoraInicio, $dataHoraFim])
                    ->orWhere(function ($q) use ($dataHoraInicio, $dataHoraFim) {
                        $q->where('data_hora_inicio', '<=', $dataHoraInicio)
                          ->where('data_hora_fim', '>=', $dataHoraFim);
                    });
            })
            ->exists();
    }

    /**
     * Sugere horários disponíveis para agendamento
     */
    public function sugerirHorarios(Sessao $sessao, Carbon $data, $duracaoMinutos = 60)
    {
        $horariosDisponiveis = [];
        $inicioDia = $data->copy()->setTime(8, 0); // Horário comercial início
        $fimDia = $data->copy()->setTime(18, 0);  // Horário comercial fim

        $horarioAtual = $inicioDia->copy();

        while ($horarioAtual < $fimDia) {
            $horarioFim = $horarioAtual->copy()->addMinutes($duracaoMinutos);

            if ($this->profissionalDisponivel($sessao->profissional_id, $horarioAtual, $horarioFim)) {
                $horariosDisponiveis[] = [
                    'inicio' => $horarioAtual->format('H:i'),
                    'fim' => $horarioFim->format('H:i'),
                ];
            }

            $horarioAtual->addMinutes(30); // Intervalo de 30 minutos
        }

        return $horariosDisponiveis;
    }
}