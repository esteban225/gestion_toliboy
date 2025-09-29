<?php

use App\Modules\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas de autenticación para la API.
 *
 * - POST 'login': Inicia sesión de usuario mediante AuthController@login.
 *
 * Grupo protegido por los middlewares 'api', 'jwt.auth' y SetDbSessionUser:
 *   - POST 'refresh': Refresca el token JWT mediante AuthController@refresh.
 *   - POST 'logout': Cierra la sesión del usuario mediante AuthController@logout.
 *   - GET 'data/me': Obtiene los datos del usuario autenticado mediante AuthController@me.
 *
 * Grupo con prefijo 'auth' y protección adicional por roles ('DEV', 'GG', 'INGPL', 'INGPR'):
 *   - POST 'register': Registra un nuevo usuario mediante AuthController@register.
 */

/**
 * POST /login
 * Inicia sesión de usuario y retorna el token JWT.
 *
 * @see AuthController@login
 */
Route::post('login', [AuthController::class, 'login']);

// Grupo protegido por autenticación JWT y sesión de usuario
Route::middleware(['api', 'jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    /**
     * POST /refresh
     * Refresca el token JWT del usuario autenticado.
     *
     * @see AuthController@refresh
     */
    Route::post('refresh', [AuthController::class, 'refresh']);

    /**
     * POST /logout
     * Cierra la sesión del usuario autenticado.
     *
     * @see AuthController@logout
     */
    Route::post('logout', [AuthController::class, 'logout']);

    /**
     * GET /data/me
     * Obtiene los datos del usuario autenticado.
     *
     * @see AuthController@me
     */
    Route::get('data/me', [AuthController::class, 'me']);
});

// Grupo con prefijo 'auth' y protección adicional por roles
Route::prefix('auth')->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    /**
     * POST /auth/register
     * Registra un nuevo usuario en el sistema.
     *
     * @see AuthController@register
     */
    Route::post('register', [AuthController::class, 'register']);
});
