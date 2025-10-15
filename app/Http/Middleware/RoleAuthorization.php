<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleAuthorization
{
    /**
     * Maneja la autorización basada en roles con JWT.
     */
    public function handle(Request $request, Closure $next, $role): JsonResponse|\Symfony\Component\HttpFoundation\Response
    {
        try {
            // Verificar que el token exista
            if (! $request->bearerToken()) {
                return $this->unauthorized('Token no proporcionado. Inicia sesión nuevamente.');
            }

            // Autenticar usuario desde el token
            $user = JWTAuth::parseToken()->authenticate();

            if (! $user) {
                return $this->unauthorized('Usuario no encontrado o no válido.');
            }

            // Obtener el rol del usuario
            $userRole = strtoupper($this->getUserRoleFromToken($user));
            $allowedRoles = array_map('strtoupper', preg_split('/[,\|]/', $role));

            if (! in_array($userRole, $allowedRoles)) {
                return $this->forbidden('No tienes permiso para acceder a este recurso.');
            }

            return $next($request);

        } catch (TokenExpiredException $e) {
            return $this->unauthorized('Tu token ha expirado. Por favor, vuelve a iniciar sesión.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorized('Tu token no es válido. Vuelve a iniciar sesión.');
        } catch (JWTException $e) {
            return $this->unauthorized('No se pudo procesar tu token. Adjunta un token válido en el encabezado Authorization.');
        } catch (\Exception $e) {
            // Captura cualquier otra excepción inesperada
            return $this->unauthorized('Error al procesar la autenticación: '.$e->getMessage());
        }
    }

    /**
     * Extrae el rol del token o del usuario autenticado.
     */
    private function getUserRoleFromToken($user): ?string
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();

            if ($payload->get('role')) {
                return $payload->get('role');
            }

            if ($payload->get('role_name')) {
                return $payload->get('role_name');
            }
        } catch (\Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException $e) {
            if (str_contains($e->getMessage(), 'The token has been blacklisted')) {
                return $this->unauthorized('Tu sesión ha expirado. Inicia sesión nuevamente.');
            }

            return $this->unauthorized('Token no autorizado o inválido.');
        }

        return $user->role->name ?? null;
    }

    /**
     * Respuesta estándar para errores de autenticación.
     */
    private function unauthorized(string $message = 'No autenticado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 'UNAUTHORIZED',
            'message' => $message,
        ], 401);
    }

    /**
     * Respuesta estándar para errores de autorización.
     */
    private function forbidden(string $message = 'Acceso denegado'): JsonResponse
    {
        return response()->json([
            'success' => false,
            'code' => 'FORBIDDEN',
            'message' => $message,
        ], 403);
    }
}
