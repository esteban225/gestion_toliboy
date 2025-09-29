<?php

/**
 * Rutas del módulo Users
 *
 * Agrupa las rutas relacionadas con la gestión de usuarios, incluyendo
 * consulta, registro, actualización y eliminación de usuarios.
 */

use App\Modules\Users\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;

// Definición de constante para evitar duplicación de la ruta de usuario por ID
const USER_ID_ROUTE = '/users/{id}';

// Rutas protegidas para la gestión de usuarios
Route::middleware(['api', 'jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    /**
     * GET /users/{id}
     * Obtiene un usuario por su ID.
     *
     * @param  int  $id  ID del usuario
     */
    Route::get(USER_ID_ROUTE, [UsersController::class, 'show']);

    /**
     * PUT /users/{id}
     * Actualiza un usuario existente.
     *
     * @param  int  $id  ID del usuario
     */
    Route::put(USER_ID_ROUTE, [UsersController::class, 'update']);
});

Route::prefix('accesData')->group(function () {
    /**
     * GET /users
     * Lista todos los usuarios paginados y filtrados.
     */
    Route::get('/users', [UsersController::class, 'index'])->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class]);

    /**
     * DELETE /users/{id}
     * Elimina un usuario por su ID.
     *
     * @param  int  $id  ID del usuario
     */
    Route::delete(USER_ID_ROUTE, [UsersController::class, 'destroy'])->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class]);

    /**
     * POST /users
     * Crea un nuevo usuario.
     */
    Route::post('/users', [UsersController::class, 'store'])->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class]);
});
