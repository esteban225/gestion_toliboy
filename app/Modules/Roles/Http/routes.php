<?php

use App\Modules\Roles\Http\Controllers\RolesController;
use Illuminate\Support\Facades\Route;

// ✅ Evita redefinir la constante si el archivo se carga varias veces
if (! defined('ROLE_ID_ROUTE')) {
    define('ROLE_ID_ROUTE', '/roles/{id}');
}

Route::middleware(['api', 'jwt.auth', 'role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get(ROLE_ID_ROUTE, [RolesController::class, 'getById']);
    Route::get('/roles', [RolesController::class, 'list']);
    Route::post('/roles', [RolesController::class, 'create']);
    Route::put(ROLE_ID_ROUTE, [RolesController::class, 'update']);
    Route::delete(ROLE_ID_ROUTE, [RolesController::class, 'delete']);
});
