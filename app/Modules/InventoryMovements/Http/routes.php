<?php

use App\Modules\InventoryMovements\Http\Controllers\InvMoveController;
use Illuminate\Support\Facades\Route;

Route::prefix('inventory-movements')->middleware(['api', 'jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {

    Route::get('/', [InvMoveController::class, 'index']);
    Route::get('/{id}', [InvMoveController::class, 'show']);
    Route::post('/', [InvMoveController::class, 'store']);
    Route::put('/{id}', [InvMoveController::class, 'update']);
    Route::delete('/{id}', [InvMoveController::class, 'destroy']);
});
