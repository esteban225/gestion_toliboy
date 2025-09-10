<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleAuthorization
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        try {
            // Obtener el usuario autenticado desde el token
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorized('Usuario no encontrado');
            }

            // Obtener el rol del usuario desde el token o desde la base de datos
            $userRole = $this->getUserRoleFromToken($user);

            // Convertir string de roles permitidos en array
            $allowedRoles = explode(',', $role);

            // Verificar si el rol del usuario está permitido
            if (in_array($userRole, $allowedRoles)) {
                return $next($request);
            }

            return $this->forbidden();

        } catch (TokenExpiredException $e) {
            return $this->unauthorized('Tu token ha expirado. Por favor, vuelve a iniciar sesión.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorized('Tu token no es válido. Vuelve a iniciar sesión.');
        } catch (JWTException $e) {
            return $this->unauthorized('Por favor, adjunte un Token de Portador a su solicitud');
        }
    }

    /**
     * Obtener el rol del usuario desde el token o la base de datos
     */
    private function getUserRoleFromToken($user)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            // Verificar si el claim "role" existe
            $role = $payload->get('role');
            if (!is_null($role)) {
                return $role;
            }

            // Verificar si el claim "role_name" existe
            $roleName = $payload->get('role_name');
            if (!is_null($roleName)) {
                return $roleName;
            }
        } catch (\Exception $e) {
            // Fallback a la base de datos si no está en el token
        }

        // Si no está en el token, obtener de la base de datos
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        return $user->role->name;
    }

    private function unauthorized($message = null)
    {
        return response()->json([
            'message' => $message ? $message : 'No autenticado',
            'success' => false
        ], 401);
    }

    private function forbidden($message = null)
    {
        return response()->json([
            'message' => $message ? $message : 'No tiene permisos para acceder a este recurso',
            'success' => false
        ], 403);
    }
}
