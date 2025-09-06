<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Manejar la solicitud entrante.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Verifica autenticación con Sanctum
        if (!$request->user()) {
            return response()->json(['message' => 'No autenticado'], 401);
        }

        // Rol actual del usuario autenticado
        $userRole = $request->user()->role;

        // Validar si el rol del usuario está dentro de los permitidos
        if (!in_array($userRole, $roles)) {
            return response()->json(['message' => 'No tienes permisos para acceder a esta ruta'], 403);
        }

        return $next($request);
    }
}
