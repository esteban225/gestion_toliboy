<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SetDbSessionUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Solo registra la sesión si estamos usando MySQL y hay un usuario autenticado
            if (config('database.default') === 'mysql' && Auth::check()) {
                $userId = Auth::id();
                $ip = $request->ip() ?? '0.0.0.0';
                $userAgent = substr($request->userAgent() ?? 'Unknown', 0, 1000);

                // Generamos un ID de sesión tipo string (UUID v4)
                $sessionId = (string) Str::uuid();

                // Llamamos al procedimiento almacenado con 4 parámetros
                DB::statement('CALL ftoliboy_toliboy_data.set_current_user(?, ?, ?, ?)', [
                    $sessionId,
                    $userId,
                    $ip,
                    $userAgent,
                ]);
            }
        } catch (\Throwable $e) {
            // Registra el error pero no bloquea la petición
            Log::warning('Error al registrar sesión en BD: '.$e->getMessage());
        }

        return $next($request);
    }
}
