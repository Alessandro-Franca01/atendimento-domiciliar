<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProfessionalAppointmentController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:professional');
    }

    public function index()
    {
        $professional = Auth::guard('professional')->user();
        
        $appointments = Appointment::with(['patient', 'address', 'therapySession'])
            ->whereHas('therapySession', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->orderBy('data_hora_inicio', 'desc')
            ->paginate(15);

        return view('professional.appointments.index', compact('appointments'));
    }

    public function calendar()
    {
        $professional = Auth::guard('professional')->user();
        
        $appointments = Appointment::with(['patient', 'therapySession'])
            ->whereHas('therapySession', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereMonth('data_hora_inicio', Carbon::now()->month)
            ->orderBy('data_hora_inicio')
            ->get();

        return view('professional.appointments.calendar', compact('appointments'));
    }

    public function show(Appointment $appointment)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($appointment->therapySession->professional_id !== $professional->id) {
            abort(403);
        }

        $appointment->load(['patient.addresses', 'address', 'therapySession', 'attendance']);

        return view('professional.appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($appointment->therapySession->professional_id !== $professional->id) {
            abort(403);
        }

        $appointment->load(['patient.addresses', 'therapySession']);

        return view('professional.appointments.edit', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($appointment->therapySession->professional_id !== $professional->id) {
            abort(403);
        }

        $request->validate([
            'data_hora_inicio' => 'required|date',
            'data_hora_fim' => 'required|date|after:data_hora_inicio',
            'address_id' => 'nullable|exists:addresses,id',
            'status' => 'required|in:agendado,confirmado,cancelado,concluido,faltou',
            'observacoes' => 'nullable|string',
        ]);

        if ($request->address_id) {
            $addressBelongsToPatient = $appointment->patient->addresses()
                ->where('id', $request->address_id)
                ->exists();
            
            if (!$addressBelongsToPatient) {
                return redirect()->back()->with('error', 'Endereço inválido para este paciente.');
            }
        }

        $appointment->update([
            'data_hora_inicio' => $request->data_hora_inicio,
            'data_hora_fim' => $request->data_hora_fim,
            'address_id' => $request->address_id,
            'status' => $request->status,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('professional.appointments.show', $appointment)
            ->with('success', 'Agendamento atualizado com sucesso!');
    }

    public function confirm(Appointment $appointment)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($appointment->therapySession->professional_id !== $professional->id) {
            abort(403);
        }

        $appointment->update(['status' => 'confirmado']);

        return redirect()->back()->with('success', 'Agendamento confirmado com sucesso!');
    }

    public function cancel(Appointment $appointment)
    {
        $professional = Auth::guard('professional')->user();
        
        if ($appointment->therapySession->professional_id !== $professional->id) {
            abort(403);
        }

        if ($appointment->status === 'concluido') {
            return redirect()->back()->with('error', 'Não é possível cancelar um agendamento já concluído.');
        }

        $appointment->update(['status' => 'cancelado']);

        return redirect()->back()->with('success', 'Agendamento cancelado com sucesso!');
    }

    public function today()
    {
        $professional = Auth::guard('professional')->user();
        $today = Carbon::today();
        
        $appointments = Appointment::with(['patient', 'address', 'therapySession'])
            ->whereHas('therapySession', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereDate('data_hora_inicio', $today)
            ->whereIn('status', ['agendado', 'confirmado'])
            ->orderBy('data_hora_inicio')
            ->get();

        return view('professional.appointments.today', compact('appointments'));
    }

    public function week()
    {
        $professional = Auth::guard('professional')->user();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        
        $appointments = Appointment::with(['patient', 'address', 'therapySession'])
            ->whereHas('therapySession', function($query) use ($professional) {
                $query->where('professional_id', $professional->id);
            })
            ->whereBetween('data_hora_inicio', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['agendado', 'confirmado'])
            ->orderBy('data_hora_inicio')
            ->get()
            ->groupBy(function ($appointment) {
                return Carbon::parse($appointment->data_hora_inicio)->format('Y-m-d');
            });

        return view('professional.appointments.week', compact('appointments'));
    }
}