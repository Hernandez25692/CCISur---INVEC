<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordChanged
{
    /**
     * Comprobar que los usuarios nuevos hayan establecido su contraseña personal obligatoria.
     * Si no lo han hecho, se restringe la navegación al dashboard donde se les presenta el modal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->debeCambiarPassword()) {
            // Permitir únicamente la acción de cambio de contraseña, el logout y la visualización del dashboard
            if ($request->routeIs('primer-cambio-password', 'logout', 'dashboard')) {
                return $next($request);
            }

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Debes establecer tu contraseña personal antes de continuar.',
                ], 403);
            }

            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
