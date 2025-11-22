<?php

namespace App\Http\Controllers;

use App\Models\TherapySession;
use App\Models\Patient;
use App\Models\SessionSchedule;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfessionalSessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:professional');
    }

    public function index()
    {
        $professional = Auth::guard('professional')->user();
        $sessions = TherapySession::with(['patient', 'sessionSchedules'])
            ->where('professional_id', $professional->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('professional.sessions.index', compact('sessions'));
    }

    public function create()
    {
        $professional = Auth::guard('professional')->user();
        $patients = Patient::whereHas('therapySessions', function($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        })->orWhereDoesntHave('therapySessions')->get();

        return view('professional.sessions.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $professional = Auth::guard('professional')->user();
        
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_por_sessao' => 'required|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'observacoes' => 'nullable|string',
        ]);

        $valorComDesconto = $request->valor_por_sessao * (1 - ($request->desconto_percentual ?? 0) / 100);
        $valorTotal = $valorComDesconto * $request->total_sessoes;

        $session = TherapySession::create([
            'patient_id' => $request->patient_id,
            'professional_id' => $professional->id,
            'descricao' => $request->descricao,
            'total_sessoes' => $request->total_sessoes,
            'sessoes_realizadas' => 0,
            'valor_por_sessao' => $request->valor_por_sessao,
            'desconto_percentual' => $request->desconto_percentual ?? 0,
            'valor_total' => $valorTotal,
            'observacoes' => $request->observacoes,
            'data_inicio' => Carbon::now(),
            'status' => 'ativo',
        ]);

        return redirect()->route('professional.sessions.show', $session)
            ->with('success', 'Sessão criada com sucesso!');
    }

    public function show(TherapySession $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        $session->load(['patient', 'sessionSchedules', 'appointments']);

        return view('professional.sessions.show', compact('session'));
    }

    public function edit(TherapySession $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        $patients = Patient::whereHas('therapySessions', function($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        })->orWhereDoesntHave('therapySessions')->get();

        return view('professional.sessions.edit', compact('session', 'patients'));
    }

    public function update(Request $request, TherapySession $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        $request->validate([
            'descricao' => 'required|string|max:255',
            'valor_por_sessao' => 'required|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'observacoes' => 'nullable|string',
            'status' => 'required|in:ativo,concluido,suspenso',
        ]);

        $valorComDesconto = $request->valor_por_sessao * (1 - ($request->desconto_percentual ?? 0) / 100);
        $valorTotal = $valorComDesconto * $session->total_sessoes;

        $session->update([
            'descricao' => $request->descricao,
            'valor_por_sessao' => $request->valor_por_sessao,
            'desconto_percentual' => $request->desconto_percentual ?? 0,
            'valor_total' => $valorTotal,
            'observacoes' => $request->observacoes,
            'status' => $request->status,
        ]);

        return redirect()->route('professional.sessions.show', $session)
            ->with('success', 'Sessão atualizada com sucesso!');
    }

    public function destroy(TherapySession $session)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($session->professional_id !== $professional->id) {
            abort(403);
        }

        if ($session->appointments()->exists()) {
            return redirect()->route('professional.sessions.index')
                ->with('error', 'Não é possível excluir uma sessão que possui agendamentos.');
        }

        $session->delete();

        return redirect()->route('professional.sessions.index')
            ->with('success', 'Sessão excluída com sucesso!');
    }
}