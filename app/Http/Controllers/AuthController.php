<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|max:255',
            'password' => 'required|string',
        ], [
            'email.required' => 'Ups! Necesitas ingresar tu usuario o email.',
            'password.required' => 'Ups! No olvides tu contraseña.',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Debug: Verificar si el usuario existe
        $user = User::where('email', $request->email)->first();
        \Log::info('=== LOGIN ATTEMPT ===');
        \Log::info('Email ingresado: ' . $request->email);
        \Log::info('Usuario encontrado: ' . ($user ? 'SI (ID: ' . $user->id . ', Activo: ' . ($user->activo ? 'SI' : 'NO') . ')' : 'NO'));
        if ($user) {
            \Log::info('Password hash en DB: ' . substr($user->password, 0, 20) . '...');
            \Log::info('Verificando password: ' . (Hash::check($request->password, $user->password) ? 'CORRECTO' : 'INCORRECTO'));
        }

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            if (!Auth::user()->activo) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Ups! Tu cuenta fue desactivada. Contacta al administrador para reactivarla.',
                ])->withInput($request->only('email'));
            }

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Ups! El usuario o la contraseña no son correctos. Verifica e intenta de nuevo.',
        ])->withInput($request->only('email'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'name.required' => 'Ups! Necesitas ingresar tu nombre completo.',
            'email.required' => 'Ups! El email es obligatorio para crear tu cuenta.',
            'email.email' => 'Ups! Ese no parece ser un email valido. Ejemplo: usuario@correo.com',
            'email.unique' => 'Ups! Ya existe una cuenta con ese email. Intenta iniciar sesion.',
            'password.required' => 'Ups! Necesitas crear una contraseña.',
            'password.min' => 'Ups! La contraseña debe tener al menos 8 caracteres para mayor seguridad.',
            'password.confirmed' => 'Ups! Las contraseñas no coinciden. Verifica e intenta de nuevo.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
