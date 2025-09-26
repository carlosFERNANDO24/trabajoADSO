<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maneja la validación de roles.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user(); 

        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        // ✅ CORREGIDO: Cambiado 'rol' por 'role'
        // Si es admin, tiene acceso a todo
        if ($user->role === 'admin') {
            return $next($request);
        }

        // ✅ CORREGIDO: Cambiado 'rol' por 'role'
        // Si no es admin, validamos que su rol esté permitido
        if (!in_array($user->role, $roles)) {
            return response()->json(['error' => 'No tienes acceso a esta ruta'], 403);
        }

        return $next($request);
    }
}
