<?php

use App\Modules\Batches\Http\Controllers\BatcheController;
use Illuminate\Support\Facades\Route;

Route::prefix('batches')->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('/', [BatcheController::class, 'index']);
    Route::get('/{id}', [BatcheController::class, 'show']);
    Route::post('/', [BatcheController::class, 'store']);
    Route::put('/{id}', [BatcheController::class, 'update']);
    Route::delete('/{id}', [BatcheController::class, 'destroy']);
});
