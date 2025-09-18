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
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return $this->unauthorized('Usuario no encontrado');
            }

            $userRole = strtoupper($this->getUserRoleFromToken($user));
            $allowedRoles = array_map('strtoupper', preg_split('/[,\|]/', $role));

            if (in_array($userRole, $allowedRoles)) {
                return $next($request);
            }

            return $this->forbidden();

        } catch (TokenExpiredException $e) {
            return $this->unauthorized('Tu token ha expirado. Por favor, vuelve a iniciar sesión.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorized('Tu token no es válido. Vuelve a iniciar sesión.');
        } catch (JWTException $e) {
            return $this->unauthorized('Por favor, adjunta un Token de Portador a tu solicitud.');
        }
    }

    private function getUserRoleFromToken($user)
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            if ($payload->get('role')) {
                return $payload->get('role');
            }

            if ($payload->get('role_name')) {
                return $payload->get('role_name');
            }
        } catch (\Exception $e) {
            // fallback
        }

        return $user->role->name ?? null;
    }

    private function unauthorized($message = null)
    {
        return response()->json([
            'message' => $message ?? 'No autenticado',
            'success' => false,
            'code' => 'UNAUTHORIZED'
        ], 401);
    }

    private function forbidden($message = null)
    {
        return response()->json([
            'message' => $message ?? 'No tiene permisos para acceder a este recurso',
            'success' => false,
            'code' => 'FORBIDDEN'
        ], 403);
    }
}
