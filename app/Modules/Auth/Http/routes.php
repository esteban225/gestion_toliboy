<?php

use App\Modules\Auth\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas de autenticación para la API.
 *
 * POST /login
 *   - Inicia sesión de usuario mediante AuthController@login.
 * GET data/me
 *   - Obtiene el usuario autenticado con jwt
 *
 * Grupo de rutas bajo el prefijo 'auth' con los siguientes middlewares:
 *   - 'api'
 *   - 'jwt.auth': Requiere autenticación JWT.
 *   - 'role:DEV,GG,INGPL,INGPR': Restringe acceso a usuarios con roles específicos.
 *   - SetDbSessionUser: Establece la sesión de usuario en la base de datos.
 *
 * Dentro del grupo:
 *
 *   - POST /auth/refresh: Refresca el token JWT mediante AuthController@refresh.
 *   - POST /auth/logout: Cierra la sesión del usuario mediante AuthController@logout.
 */
Route::post('login', [AuthController::class, 'login']);

Route::get('data/me', [AuthController::class, 'me']);

Route::prefix('auth')->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);
});
