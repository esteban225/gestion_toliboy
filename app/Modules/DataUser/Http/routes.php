<?php

use App\Modules\DataUser\Http\Controllers\DataUserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth', 'role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {

    Route::get('/dataUsers', [DataUserController::class, 'index']);
    Route::get('/dataUsers/{id}', [DataUserController::class, 'show']);
    Route::post('/dataUsers', [DataUserController::class, 'store']);
    Route::put('/dataUsers/{id}', [DataUserController::class, 'update']);
    Route::delete('/dataUsers/{id}', [DataUserController::class, 'destroy']);
});
