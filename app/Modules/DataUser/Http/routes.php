<?php

use App\Modules\DataUser\Http\Controllers\DataUserController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas del módulo DataUser
 *
 * Agrupa las rutas relacionadas con la gestión de datos de usuario, incluyendo
 * consulta, registro, actualización y eliminación de datos de usuario.
 */

// Rutas protegidas para la gestión de datos de usuario
Route::middleware(['api', 'jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    /**
     * GET /dataUsers/{id}
     * Obtiene los datos de usuario por su ID.
     *
     * @param  int  $id  ID del usuario
     */
    Route::get('/dataUsers/{id}', [DataUserController::class, 'show']);

    /**
     * POST /dataUsers
     * Crea un nuevo registro de datos de usuario.
     */
    Route::post('/dataUsers', [DataUserController::class, 'store']);

    /**
     * PUT /dataUsers/{id}
     * Actualiza los datos de usuario existentes.
     *
     * @param  int  $id  ID del usuario
     */
    Route::put('/dataUsers/{id}', [DataUserController::class, 'update']);
});

// Rutas con prefijo 'accesData' y protección adicional por roles
Route::prefix('accesData')->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    /**
     * DELETE /accesData/dataUsers/{id}
     * Elimina los datos de usuario por su ID.
     *
     * @param  int  $id  ID del usuario
     */
    Route::delete('/dataUsers/{id}', [DataUserController::class, 'destroy']);

    /**
     * GET /accesData/dataUsers
     * Lista todos los datos de usuario paginados y filtrados.
     */
    Route::get('/dataUsers', [DataUserController::class, 'index']);
});
