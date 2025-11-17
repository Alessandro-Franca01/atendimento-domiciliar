<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Professional;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::with(['patient','professional','appointment'])->latest()->paginate(15);
        return view('attendances.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $patients = Patient::where('status','ativo')->get();
        $professionals = Professional::where('status','ativo')->get();
        $appointments = Appointment::latest()->get();
        return view('attendances.create', compact('patients','professionals','appointments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id' => ['required','exists:patients,id'],
            'professional_id' => ['required','exists:professionals,id'],
            'appointment_id' => ['nullable','exists:appointments,id'],
            'data_realizacao' => ['required','date'],
            'valor' => ['nullable','numeric','min:0'],
            'procedimento_realizado' => ['nullable','string'],
            'evolucao' => ['nullable','string'],
            'assinatura_paciente' => ['nullable','string'],
            'status' => ['required','in:concluido,interrompido'],
        ]);

        $attendance = Attendance::create($data);

        return redirect()->route('attendances.show', $attendance)
            ->with('success', 'Attendance created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load(['patient','professional','appointment']);
        return view('attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $patients = Patient::where('status','ativo')->get();
        $professionals = Professional::where('status','ativo')->get();
        $appointments = Appointment::latest()->get();
        return view('attendances.edit', compact('attendance','patients','professionals','appointments'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $data = $request->validate([
            'patient_id' => ['required','exists:patients,id'],
            'professional_id' => ['required','exists:professionals,id'],
            'appointment_id' => ['nullable','exists:appointments,id'],
            'data_realizacao' => ['required','date'],
            'valor' => ['nullable','numeric','min:0'],
            'procedimento_realizado' => ['nullable','string'],
            'evolucao' => ['nullable','string'],
            'assinatura_paciente' => ['nullable','string'],
            'status' => ['required','in:concluido,interrompido'],
        ]);

        $attendance->update($data);

        return redirect()->route('attendances.show', $attendance)
            ->with('success', 'Attendance updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('attendances.index')
            ->with('success', 'Attendance deleted successfully.');
    }
}