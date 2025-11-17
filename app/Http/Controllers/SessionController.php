<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Professional;
use App\Models\Session;
use App\Models\SessionSchedule;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessions = Session::with(['patient', 'professional'])
            ->orderBy('data_inicio', 'desc')
            ->paginate(10);

        return view('sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::where('status', 'ativo')->orderBy('nome')->get();
        $professionals = Professional::where('status', 'ativo')->orderBy('nome')->get();

        return view('sessions.create', compact('patients', 'professionals'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'professional_id' => 'required|exists:professionals,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_por_sessao' => 'nullable|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:ativo,inativo,concluido',
        ]);

        $session = Session::create($request->all());

        return redirect()->route('sessions.index')
            ->with('success', 'Session created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Session $session)
    {
        $session->load(['patient', 'professional', 'sessionSchedules', 'appointments']);
        return view('sessions.show', compact('session'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Session $session)
    {
        $patients = Patient::where('status', 'ativo')->orderBy('nome')->get();
        $professionals = Professional::where('status', 'ativo')->orderBy('nome')->get();

        return view('sessions.edit', compact('session', 'patients', 'professionals'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Session $session)
    {
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'professional_id' => 'required|exists:professionals,id',
            'descricao' => 'required|string|max:255',
            'total_sessoes' => 'required|integer|min:1',
            'valor_por_sessao' => 'nullable|numeric|min:0',
            'desconto_percentual' => 'nullable|numeric|min:0|max:100',
            'data_inicio' => 'required|date',
            'data_fim_prevista' => 'nullable|date|after_or_equal:data_inicio',
            'status' => 'required|in:ativo,inativo,concluido',
        ]);

        $session->update($request->all());

        return redirect()->route('sessions.index')
            ->with('success', 'Session updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Session $session)
    {
        if ($session->appointments()->exists()) {
            return redirect()->route('sessions.index')
                ->with('error', 'Cannot delete session with appointments.');
        }

        $session->delete();

        return redirect()->route('sessions.index')
            ->with('success', 'Session deleted successfully.');
    }