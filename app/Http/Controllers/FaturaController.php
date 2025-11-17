<?php

namespace App\Http\Controllers;

use App\Models\Fatura;
use App\Models\Paciente;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FaturaController extends Controller
{
    public function index()
    {
        $faturas = Fatura::with('paciente')->latest()->paginate(15);
        return view('financeiro.faturas.index', compact('faturas'));
    }

    public function show(Fatura $fatura)
    {
        $fatura->load(['paciente', 'itens', 'pagamentos']);
        return view('financeiro.faturas.show', compact('fatura'));
    }

    public function createMensal()
    {
        $pacientes = Paciente::where('status', 'ativo')->get();
        return view('financeiro.faturas.create_mensal', compact('pacientes'));
    }

    public function storeMensal(Request $request, FinanceService $finance)
    {
        $data = $request->validate([
            'paciente_id' => ['required','exists:pacientes,id'],
            'mes' => ['required','date_format:Y-m'],
        ]);

        $mesRef = Carbon::createFromFormat('Y-m', $data['mes'])->startOfMonth();
        $fatura = $finance->gerarFaturaMensal((int) $data['paciente_id'], $mesRef);

        return redirect()->route('faturas.show', $fatura)->with('success', 'Fatura mensal gerada.');
    }
}