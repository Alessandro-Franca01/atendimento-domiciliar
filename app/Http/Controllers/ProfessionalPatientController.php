<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfessionalPatientController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth:professional');
    }

    public function index()
    {
        $professional = Auth::guard('professional')->user();
        
        $patients = Patient::whereHas('therapySessions', function($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        })->with(['addresses', 'therapySessions' => function($query) use ($professional) {
            $query->where('professional_id', $professional->id);
        }])->orderBy('nome')->paginate(10);

        return view('professional.patients.index', compact('patients'));
    }

    public function create()
    {
        return view('professional.patients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email',
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'nullable|date',
            'cpf' => 'nullable|string|max:20|unique:patients,cpf',
            'numero_whatsapp' => 'nullable|string|max:20',
            'convenio' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string',
            'endereco' => 'required|array',
            'endereco.cep' => 'required|string|max:10',
            'endereco.logradouro' => 'required|string|max:255',
            'endereco.numero' => 'required|string|max:20',
            'endereco.complemento' => 'nullable|string|max:100',
            'endereco.bairro' => 'required|string|max:100',
            'endereco.cidade' => 'required|string|max:100',
            'endereco.estado' => 'required|string|max:2',
        ]);

        $patient = Patient::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'data_nascimento' => $request->data_nascimento,
            'cpf' => $request->cpf,
            'numero_whatsapp' => $request->numero_whatsapp,
            'convenio' => $request->convenio,
            'observacoes' => $request->observacoes,
        ]);

        // Criar endereço
        Address::create([
            'patient_id' => $patient->id,
            'cep' => $request->endereco['cep'],
            'logradouro' => $request->endereco['logradouro'],
            'numero' => $request->endereco['numero'],
            'complemento' => $request->endereco['complemento'] ?? null,
            'bairro' => $request->endereco['bairro'],
            'cidade' => $request->endereco['cidade'],
            'estado' => $request->endereco['estado'],
            'principal' => true,
        ]);

        return redirect()->route('professional.patients.show', $patient)
            ->with('success', 'Paciente criado com sucesso!');
    }

    public function show(Patient $patient)
    {
        $professional = Auth::guard('professional')->user();
        
        // Verificar se o paciente tem sessões com este profissional
        $hasAccess = $patient->sessions()->where('professional_id', $professional->id)->exists();
        
        if (!$hasAccess) {
            abort(403);
        }

        $patient->load(['addresses', 'sessions' => function($query) use ($professional) {
            $query->where('professional_id', $professional->id)->with('appointments');
        }]);

        return view('professional.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        $professional = Auth::guard('professional')->user();
        
        $hasAccess = $patient->sessions()->where('professional_id', $professional->id)->exists();
        
        if (!$hasAccess) {
            abort(403);
        }

        $patient->load('addresses');

        return view('professional.patients.edit', compact('patient'));
    }

    public function update(Request $request, Patient $patient)
    {
        $professional = Auth::guard('professional')->user();
        
        $hasAccess = $patient->sessions()->where('professional_id', $professional->id)->exists();
        
        if (!$hasAccess) {
            abort(403);
        }

        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'nullable|email|unique:patients,email,' . $patient->id,
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'nullable|date',
            'cpf' => 'nullable|string|max:20|unique:patients,cpf,' . $patient->id,
            'numero_whatsapp' => 'nullable|string|max:20',
            'convenio' => 'nullable|string|max:100',
            'observacoes' => 'nullable|string',
        ]);

        $patient->update([
            'nome' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'data_nascimento' => $request->data_nascimento,
            'cpf' => $request->cpf,
            'numero_whatsapp' => $request->numero_whatsapp,
            'convenio' => $request->convenio,
            'observacoes' => $request->observacoes,
        ]);

        return redirect()->route('professional.patients.show', $patient)
            ->with('success', 'Paciente atualizado com sucesso!');
    }

    public function addAddress(Request $request, Patient $patient)
    {
        $professional = Auth::guard('professional')->user();
        
        $hasAccess = $patient->sessions()->where('professional_id', $professional->id)->exists();
        
        if (!$hasAccess) {
            abort(403);
        }

        $request->validate([
            'cep' => 'required|string|max:10',
            'logradouro' => 'required|string|max:255',
            'numero' => 'required|string|max:20',
            'complemento' => 'nullable|string|max:100',
            'bairro' => 'required|string|max:100',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|max:2',
            'principal' => 'boolean',
        ]);

        // Se for endereço principal, desmarcar outros
        if ($request->principal) {
            $patient->addresses()->update(['principal' => false]);
        }

        Address::create([
            'patient_id' => $patient->id,
            'cep' => $request->cep,
            'logradouro' => $request->logradouro,
            'numero' => $request->numero,
            'complemento' => $request->complemento,
            'bairro' => $request->bairro,
            'cidade' => $request->cidade,
            'estado' => $request->estado,
            'principal' => $request->principal ?? false,
        ]);

        return redirect()->back()->with('success', 'Endereço adicionado com sucesso!');
    }
}