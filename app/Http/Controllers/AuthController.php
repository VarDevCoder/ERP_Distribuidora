<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'usu_email' => 'required|email',
            'usu_pass' => 'required|min:6'
        ]);

        $email = $request->input('usu_email');
        $pass = md5($request->input('usu_pass'));

        $usuario = DB::table('usuario')
            ->where('usu_email', $email)
            ->where('usu_pass', $pass)
            ->where('usu_estado', 'ACTIVO')
            ->first();

        if ($usuario) {
            session(['usuario' => $usuario]);
            return redirect()->route('dashboard');
        } else {
            return back()->with('error', 'Correo o contraseña incorrectos.');
        }
    }

    public function logout()
    {
        session()->forget('usuario');
        return redirect()->route('login');
    }
}
