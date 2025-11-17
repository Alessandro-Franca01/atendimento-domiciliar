<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Patient;
use App\Services\FinanceService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('patient')->latest()->paginate(15);
        return view('financeiro.invoices.index', compact('invoices'));
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'items', 'payments']);
        return view('financeiro.invoices.show', compact('invoice'));
    }

    public function createMonthly()
    {
        $patients = Patient::where('status', 'ativo')->get();
        return view('financeiro.invoices.create_monthly', compact('patients'));
    }

    public function storeMonthly(Request $request, FinanceService $finance)
    {
        $data = $request->validate([
            'patient_id' => ['required','exists:patients,id'],
            'mes' => ['required','date_format:Y-m'],
        ]);

        $monthRef = Carbon::createFromFormat('Y-m', $data['mes'])->startOfMonth();
        $invoice = $finance->generateMonthlyInvoice((int) $data['patient_id'], $monthRef);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Monthly invoice generated.');
    }
}