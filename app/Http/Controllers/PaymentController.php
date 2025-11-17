<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Patient;
use App\Models\Professional;
use App\Models\Session;
use App\Models\Payment;
use App\Services\FinanceService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['patient', 'professional', 'session', 'attendance'])->latest()->paginate(15);
        return view('financeiro.payments.index', compact('payments'));
    }

    public function create()
    {
        $patients = Patient::where('status', 'ativo')->get();
        $professionals = Professional::where('status', 'ativo')->get();
        $sessions = Session::where('status', 'ativo')->get();
        $attendances = Attendance::all();
        return view('financeiro.payments.create', compact('patients', 'professionals', 'sessions', 'attendances'));
    }

    public function store(Request $request, FinanceService $finance)
    {
        $data = $request->validate([
            'patient_id' => ['required','exists:patients,id'],
            'professional_id' => ['required','exists:professionals,id'],
            'metodo_pagamento' => ['required','in:pix,dinheiro,cartao,transferencia'],
            'valor' => ['required','numeric','min:0.01'],
            'data_pagamento' => ['nullable','date'],
            'observacoes' => ['nullable','string'],
            'session_id' => ['nullable','exists:therapy_sessions,id'],
            'attendance_id' => ['nullable','exists:attendances,id'],
            'invoice_id' => ['nullable','exists:invoices,id'],
        ]);

        if (!empty($data['session_id'])) {
            $session = Session::findOrFail($data['session_id']);
            $payment = $finance->pagarSessao($session, $data);
        } elseif (!empty($data['attendance_id'])) {
            $attendance = Attendance::findOrFail($data['attendance_id']);
            $payment = $finance->pagarAtendimento($attendance, $data);
        } else {
            $payment = Payment::create($data);
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment created successfully.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['patient', 'professional', 'session', 'attendance', 'invoice']);
        return view('financeiro.payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $patients = Patient::where('status', 'ativo')->get();
        $professionals = Professional::where('status', 'ativo')->get();
        $sessions = Session::where('status', 'ativo')->get();
        $attendances = Attendance::all();
        return view('financeiro.payments.edit', compact('payment', 'patients', 'professionals', 'sessions', 'attendances'));
    }

    public function update(Request $request, Payment $payment)
    {
        $data = $request->validate([
            'patient_id' => ['required','exists:patients,id'],
            'professional_id' => ['required','exists:professionals,id'],
            'metodo_pagamento' => ['required','in:pix,dinheiro,cartao,transferencia'],
            'valor' => ['required','numeric','min:0.01'],
            'data_pagamento' => ['nullable','date'],
            'observacoes' => ['nullable','string'],
            'session_id' => ['nullable','exists:therapy_sessions,id'],
            'attendance_id' => ['nullable','exists:attendances,id'],
            'invoice_id' => ['nullable','exists:invoices,id'],
        ]);

        $payment->update($data);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Payment updated successfully.');
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->route('payments.index')
            ->with('success', 'Payment deleted successfully.');
    }
}