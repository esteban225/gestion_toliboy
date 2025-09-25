<?php

use App\Modules\Batches\Http\Controllers\BatcheController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth', 'role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('/batche', [BatcheController::class, 'index']);
    Route::get('/batche/{id}', [BatcheController::class, 'show']);
    Route::post('/batche', [BatcheController::class, 'store']);
    Route::put('/batche/{id}', [BatcheController::class, 'update']);
    Route::delete('/batche/{id}', [BatcheController::class, 'destroy']);
});
