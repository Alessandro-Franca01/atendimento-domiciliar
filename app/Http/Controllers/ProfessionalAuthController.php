<?php

namespace App\Http\Controllers;

use App\Models\Professional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ProfessionalAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('professional.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('professional')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('professional.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function showRegisterForm()
    {
        return view('professional.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:professionals',
            'password' => 'required|string|min:8|confirmed',
            'crefito' => 'required|string|max:50|unique:professionals',
            'cpf' => 'required|string|max:14|unique:professionals',
            'telefone' => 'required|string|max:20',
            'data_nascimento' => 'nullable|date',
            'especialidades' => 'nullable',
            'horario_funcionamento' => 'nullable|string',
        ]);

        $professional = Professional::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'crefito' => $request->crefito,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
            'data_nascimento' => $request->data_nascimento,
            'especialidades' => $request->especialidades,
            'horario_funcionamento' => $request->horario_funcionamento,
            'status' => 'ativo',
        ]);

        Auth::guard('professional')->login($professional);

        return redirect()->route('professional.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('professional')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('professional.login');
    }
}