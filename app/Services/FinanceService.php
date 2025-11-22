<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Models\TherapySession;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    /**
     * Process payment for a therapy session
     */
    public function paySession(TherapySession $therapySession, array $data): Payment
    {
        return DB::transaction(function () use ($therapySession, $data) {
            if ($therapySession->valor_total === null) {
                throw new \InvalidArgumentException('Therapy session does not have a total value defined.');
            }

            $amount = (float) $data['valor'];
            $newPaidAmount = (float) $therapySession->valor_pago + $amount;
            
            if ($newPaidAmount - (float) $therapySession->valor_total > 0.00001) {
                throw new \InvalidArgumentException('Payment amount exceeds the session total.');
            }

            if (!empty($data['invoice_id'])) {
                $this->validateInvoicePaymentLimit((int) $data['invoice_id'], $amount);
            }

            $payment = Payment::create([
                'therapy_session_id' => $therapySession->id,
                'patient_id' => $therapySession->patient_id,
                'professional_id' => $therapySession->professional_id,
                'invoice_id' => $data['invoice_id'] ?? null,
                'metodo_pagamento' => $data['metodo_pagamento'],
                'valor' => $amount,
                'data_pagamento' => Carbon::parse($data['data_pagamento'] ?? Carbon::now())->toDateString(),
                'status' => $data['status'] ?? 'pago',
                'observacoes' => $data['observacoes'] ?? null,
            ]);

            $therapySession->update(['valor_pago' => $newPaidAmount]);

            // If session is fully paid, mark all attendances as paid via session
            if (abs($therapySession->valor_total - $newPaidAmount) < 0.00001) {
                $this->markSessionAttendancesAsPaid($therapySession);
            }

            return $payment;
        });
    }

    /**
     * Process payment for an attendance
     */
    public function payAttendance(Attendance $attendance, array $data): Payment
    {
        return DB::transaction(function () use ($attendance, $data) {
            $paymentDate = Carbon::parse($data['data_pagamento'] ?? Carbon::now())->toDateString();

            // Check for duplicate payment
            $duplicate = Payment::where('attendance_id', $attendance->id)
                ->whereDate('data_pagamento', $paymentDate)
                ->exists();
                
            if ($duplicate) {
                throw new \InvalidArgumentException('Duplicate payment for the same attendance on the same day.');
            }

            if (!empty($data['invoice_id'])) {
                $this->validateInvoicePaymentLimit((int) $data['invoice_id'], (float) $data['valor']);
            }

            $payment = Payment::create([
                'attendance_id' => $attendance->id,
                'patient_id' => $attendance->patient_id,
                'professional_id' => $attendance->professional_id,
                'invoice_id' => $data['invoice_id'] ?? null,
                'metodo_pagamento' => $data['metodo_pagamento'],
                'valor' => (float) $data['valor'],
                'data_pagamento' => $paymentDate,
                'status' => $data['status'] ?? 'pago',
                'observacoes' => $data['observacoes'] ?? null,
            ]);

            $attendance->update(['status_pagamento' => 'pago']);

            return $payment;
        });
    }

    /**
     * Validate that invoice payment limit is not exceeded
     */
    public function validateInvoicePaymentLimit(int $invoiceId, float $newAmount): void
    {
        $invoice = Invoice::findOrFail($invoiceId);
        $totalPayments = $invoice->payments()->where('status', '!=', 'estornado')->sum('valor');
        
        if (($totalPayments + $newAmount) - (float) $invoice->valor_total > 0.00001) {
            throw new \InvalidArgumentException('Payment amount exceeds the invoice total.');
        }
    }

    /**
     * Refund a payment
     */
    public function refundPayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            if ($payment->status === 'estornado') {
                return;
            }

            $payment->update(['status' => 'estornado']);

            // Update session paid amount if applicable
            if ($payment->therapy_session_id) {
                $session = $payment->therapySession;
                $newPaidAmount = max(0.0, (float) $session->valor_pago - (float) $payment->valor);
                $session->update(['valor_pago' => $newPaidAmount]);

                // If session has outstanding balance, mark attendances as pending
                if ($session->saldo_pagamento > 0) {
                    $this->markSessionAttendancesAsPending($session);
                }
            }

            // Update attendance payment status if applicable
            if ($payment->attendance_id) {
                $attendance = $payment->attendance;
                $attendance->update(['status_pagamento' => 'pendente']);
            }
        });
    }

    /**
     * Generate monthly invoice for a patient
     */
    public function generateMonthlyInvoice(int $patientId, Carbon $monthRef): Invoice
    {
        return DB::transaction(function () use ($patientId, $monthRef) {
            $startDate = $monthRef->copy()->startOfMonth();
            $endDate = $monthRef->copy()->endOfMonth();

            // Get all attendances for the month
            $attendances = Attendance::where('patient_id', $patientId)
                ->whereBetween('data_realizacao', [$startDate, $endDate])
                ->get();

            $totalAmount = 0.0;

            // Create invoice
            $invoice = Invoice::create([
                'patient_id' => $patientId,
                'valor_total' => 0,
                'data_emissao' => Carbon::now()->toDateString(),
                'data_vencimento' => $endDate->toDateString(),
                'status' => 'aberta',
                'tipo' => 'mensalidade',
            ]);

            // Create invoice items for each attendance
            foreach ($attendances as $attendance) {
                $itemValue = (float) ($attendance->valor ?? 0);
                if ($itemValue > 0) {
                    $totalAmount += $itemValue;
                    
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'descricao' => 'Atendimento em ' . Carbon::parse($attendance->data_realizacao)->format('d/m/Y'),
                        'quantidade' => 1,
                        'valor_unitario' => $itemValue,
                        'valor_total' => $itemValue,
                        'attendance_id' => $attendance->id,
                        'therapy_session_id' => null,
                    ]);
                }
            }

            // Update invoice total
            $invoice->update(['valor_total' => $totalAmount]);

            return $invoice;
        });
    }

    /**
     * Generate invoice for a complete therapy session
     */
    public function generateSessionInvoice(TherapySession $therapySession): Invoice
    {
        return DB::transaction(function () use ($therapySession) {
            if ($therapySession->valor_total === null) {
                throw new \InvalidArgumentException('Therapy session does not have a total value defined.');
            }

            $invoice = Invoice::create([
                'patient_id' => $therapySession->patient_id,
                'valor_total' => $therapySession->valor_total,
                'data_emissao' => Carbon::now()->toDateString(),
                'data_vencimento' => Carbon::now()->addDays(30)->toDateString(),
                'status' => 'aberta',
                'tipo' => 'sessao_completa',
            ]);

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'descricao' => $therapySession->descricao . ' (' . $therapySession->total_sessoes . ' sessões)',
                'quantidade' => $therapySession->total_sessoes,
                'valor_unitario' => $therapySession->valor_por_sessao,
                'valor_total' => $therapySession->valor_total,
                'attendance_id' => null,
                'therapy_session_id' => $therapySession->id,
            ]);

            return $invoice;
        });
    }

    /**
     * Mark all attendances of a session as paid via session
     */
    private function markSessionAttendancesAsPaid(TherapySession $therapySession): void
    {
        $appointmentIds = $therapySession->appointments()->pluck('id');
        
        Attendance::whereIn('appointment_id', $appointmentIds)
            ->update(['status_pagamento' => 'pago_via_sessao']);
    }

    /**
     * Mark all attendances of a session as pending
     */
    private function markSessionAttendancesAsPending(TherapySession $therapySession): void
    {
        $appointmentIds = $therapySession->appointments()->pluck('id');
        
        Attendance::whereIn('appointment_id', $appointmentIds)
            ->update(['status_pagamento' => 'pendente']);
    }

    /**
     * Calculate outstanding balance for a patient
     */
    public function calculatePatientBalance(int $patientId): array
    {
        $totalInvoiced = Invoice::where('patient_id', $patientId)
            ->whereIn('status', ['aberta', 'vencida'])
            ->sum('valor_total');

        $totalPaid = Payment::where('patient_id', $patientId)
            ->where('status', 'pago')
            ->sum('valor');

        $overdueInvoices = Invoice::where('patient_id', $patientId)
            ->where('status', 'aberta')
            ->where('data_vencimento', '<', Carbon::now())
            ->count();

        return [
            'total_invoiced' => $totalInvoiced,
            'total_paid' => $totalPaid,
            'outstanding_balance' => max(0, $totalInvoiced - $totalPaid),
            'overdue_invoices' => $overdueInvoices,
        ];
    }

    /**
     * Get payment statistics for a period
     */
    public function getPaymentStatistics(Carbon $startDate, Carbon $endDate): array
    {
        $payments = Payment::where('status', 'pago')
            ->whereBetween('data_pagamento', [$startDate, $endDate])
            ->get();

        $byMethod = $payments->groupBy('metodo_pagamento')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('valor'),
            ];
        });

        return [
            'total_amount' => $payments->sum('valor'),
            'total_count' => $payments->count(),
            'by_method' => $byMethod,
            'average_amount' => $payments->count() > 0 ? $payments->avg('valor') : 0,
        ];
    }
}