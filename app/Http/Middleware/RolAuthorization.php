<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolAuthorization
{
    /**
     * Manejar una solicitud entrante.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Si el usuario es ADMIN, tiene permiso para cualquier método
        if ($user && $user->rol === 'ADMIN') {
            return $next($request);
        }

        // 2. Si el usuario es CONSULTA, SOLO se le permiten métodos de lectura (GET)
        if ($user && $user->rol === 'CONSULTA') {
            if ($request->isMethod('get')) {
                return $next($request);
            }

            // Si intenta POST, PUT o DELETE, lanza el HTTP 403 exigido
            return response()->json([
                'error' => 'Acceso denegado. Tu rol de CONSULTA solo permite realizar consultas (GET).'
            ], 403);
        }

        // Si por alguna razón no tiene un rol válido
        return response()->json(['error' => 'No autorizado o rol no asignado.'], 403);
    }
}