<?php

use App\Modules\InventoryMovements\Http\Controllers\InvMoveController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth', 'role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {

    Route::get('/inventory-movements', [InvMoveController::class, 'index']);
    Route::get('/inventory-movements/{id}', [InvMoveController::class, 'show']);
    Route::post('/inventory-movements', [InvMoveController::class, 'store']);
    Route::put('/inventory-movements/{id}', [InvMoveController::class, 'update']);
    Route::delete('/inventory-movements/{id}', [InvMoveController::class, 'destroy']);
});
