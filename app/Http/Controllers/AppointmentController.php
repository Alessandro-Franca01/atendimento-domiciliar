<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Professional;
use App\Models\TherapySession;
use App\Models\Address;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        $appointments = Appointment::with(['patient', 'professional', 'therapySession', 'address'])
            ->orderBy('data_hora_inicio', 'desc')
            ->paginate(15);

        return view('appointments.index', compact('appointments'));
    }

    public function create()
    {
        $patients = Patient::where('status', 'ativo')->get();
        $professionals = Professional::where('status', 'ativo')->get();
        $sessions = TherapySession::where('status', 'ativo')->get();

        return view('appointments.create', compact('patients', 'professionals', 'sessions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'therapy_session_id' => 'required|exists:therapy_sessions,id',
            'patient_id' => 'required|exists:patients,id',
            'professional_id' => 'required|exists:professionals,id',
            'address_id' => 'required|exists:addresses,id',
            'data_hora_inicio' => 'required|date',
            'data_hora_fim' => 'required|date|after:data_hora_inicio',
            'status' => 'required|in:agendado,confirmado,cancelado,concluido,faltou',
            'observacoes' => 'nullable|string',
        ]);

        $appointment = Appointment::create($request->all());

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment created successfully.');
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['patient', 'professional', 'therapySession', 'address', 'attendance']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $patients = Patient::where('status', 'ativo')->get();
        $professionals = Professional::where('status', 'ativo')->get();
        $sessions = TherapySession::where('status', 'ativo')->get();
        $addresses = $appointment->patient->addresses;

        return view('appointments.edit', compact('appointment', 'patients', 'professionals', 'sessions', 'addresses'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $request->validate([
            'therapy_session_id' => 'required|exists:therapy_sessions,id',
            'patient_id' => 'required|exists:patients,id',
            'professional_id' => 'required|exists:professionals,id',
            'address_id' => 'required|exists:addresses,id',
            'data_hora_inicio' => 'required|date',
            'data_hora_fim' => 'required|date|after:data_hora_inicio',
            'status' => 'required|in:agendado,confirmado,cancelado,concluido,faltou',
            'observacoes' => 'nullable|string',
        ]);

        $appointment->update($request->all());

        return redirect()->route('appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment)
    {
        if ($appointment->attendance) {
            return redirect()->route('appointments.show', $appointment)
                ->with('error', 'Cannot delete appointment with attendance record.');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }

    public function confirm(Appointment $appointment)
    {
        $appointment->update(['status' => 'confirmado']);

        return redirect()->back()->with('success', 'Appointment confirmed successfully.');
    }

    public function cancel(Appointment $appointment)
    {
        if ($appointment->status === 'concluido') {
            return redirect()->back()->with('error', 'Cannot cancel a completed appointment.');
        }

        $appointment->update(['status' => 'cancelado']);

        return redirect()->back()->with('success', 'Appointment cancelled successfully.');
    }
}