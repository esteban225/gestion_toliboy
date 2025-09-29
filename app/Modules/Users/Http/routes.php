<?php

/**
 * Rutas del módulo Users
 *
 * Agrupa las rutas relacionadas con la gestión de usuarios, incluyendo
 * consulta, registro, actualización y eliminación de usuarios.
 *
 * @package App\Modules\Users\Http
 */

use App\Modules\Users\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

// Rutas protegidas para la gestión de usuarios
Route::middleware(['api', 'jwt.auth', 'role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    /**
     * GET /users
     * Lista todos los usuarios paginados y filtrados.
     */
    Route::get('/users', [UsersController::class, 'index']);

    /**
     * GET /users/{id}
     * Obtiene un usuario por su ID.
     * @param int $id ID del usuario
     */
    Route::get('/users/{id}', [UsersController::class, 'show']);

    /**
     * POST /users
     * Crea un nuevo usuario.
     */
    Route::post('/users', [UsersController::class, 'store']);

    /**
     * PUT /users/{id}
     * Actualiza un usuario existente.
     * @param int $id ID del usuario
     */
    Route::put('/users/{id}', [UsersController::class, 'update']);

    /**
     * DELETE /users/{id}
     * Elimina un usuario por su ID.
     * @param int $id ID del usuario
     */
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
});
