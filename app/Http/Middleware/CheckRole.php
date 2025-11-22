<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Verificar que el usuario tenga el rol requerido
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Roles permitidos
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Admin tiene acceso a todo
        if ($user->esAdmin()) {
            return $next($request);
        }

        // Verificar si el usuario tiene alguno de los roles permitidos
        if (!in_array($user->rol, $roles)) {
            // Redirigir según el rol del usuario
            if ($user->esProveedor()) {
                return redirect()->route('proveedor.dashboard')
                    ->with('error', 'No tienes permiso para acceder a esa sección');
            }

            return redirect()->route('dashboard')
                ->with('error', 'No tienes permiso para acceder a esa sección');
        }

        return $next($request);
    }
}
