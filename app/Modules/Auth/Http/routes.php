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
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['api', 'jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('data/me', [AuthController::class, 'me']);
});

Route::prefix('auth')->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::post('register', [AuthController::class, 'register']);
});
