<?php

use App\Modules\Reports\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

/**
 * Rutas centralizadas para reportes.
 * Requieren autenticación JWT y roles apropiados.
 */
Route::prefix('reports')->middleware(['jwt.auth'])->group(function () {
    Route::get('/', [ReportsController::class, 'index']);
    Route::get('/raw-materials', [ReportsController::class, 'rawMaterials']);
    Route::get('/raw-materials/low-stock', [ReportsController::class, 'rawMaterialsLowStock']);
    Route::get('/inventory', [ReportsController::class, 'inventory']);
    Route::get('/dashboard', [ReportsController::class, 'dashboard']);
});
