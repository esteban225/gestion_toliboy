<?php

namespace App\Modules\WorkLogs\Http;

use App\Modules\WorkLogs\Http\Controllers\WorkLogController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas del módulo WorkLogs
 *
 * Agrupa las rutas relacionadas con la gestión de registros de trabajo (WorkLogs),
 * incluyendo consulta, registro, actualización, eliminación y registro automático de horas.
 */


// Rutas para consultar los registros de horas de un usuario específico
Route::middleware(['api', 'jwt.auth'])->group(function () {
    /**
     * GET /hoursLog/users/{userId}
     * Obtiene los registros de horas trabajadas de un usuario por su ID.
     * Requiere autenticación JWT.
     *
     * @param  int  $userId  ID del usuario
     * @return JSON Lista de registros de horas
     */
    Route::get('hoursLog/users/{userId}', [WorkLogController::class, 'showUserWorkLogs']);
});

// Rutas protegidas para la gestión completa de WorkLogs
Route::prefix('work-logs')
    ->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])
    ->group(function () {
        /**
         * GET /work-logs
         * Lista todos los registros de trabajo paginados y filtrados.
         */
        Route::get('/', [WorkLogController::class, 'index']);

        /**
         * POST /work-logs
         * Crea un nuevo registro de trabajo.
         */
        Route::post('/', [WorkLogController::class, 'store']);

        /**
         * GET /work-logs/{id}
         * Obtiene un registro de trabajo por su ID.
         */
        Route::get('/{id}', [WorkLogController::class, 'show']);

        /**
         * PUT /work-logs/{id}
         * Actualiza un registro de trabajo existente.
         */
        Route::put('/{id}', [WorkLogController::class, 'update']);

        /**
         * DELETE /work-logs/{id}
         * Elimina un registro de trabajo por su ID.
         */
        Route::delete('/{id}', [WorkLogController::class, 'destroy']);

        /**
         * POST /work-logs/register/{id}
         * Registra automáticamente la hora de entrada o salida del usuario.
         *
         * @param  int  $id  ID del usuario
         */
        Route::post('/register/{id}', [WorkLogController::class, 'registerWorkLog']);
    });
