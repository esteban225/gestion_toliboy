<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Users\Http\Controllers\UsersController;

Route::middleware(['api', 'jwt.auth', 'role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {

    Route::get('/users', [UsersController::class, 'index']);
    Route::get('/users/{id}', [UsersController::class, 'show']);
    Route::post('/users', [UsersController::class, 'store']);
    Route::put('/users/{id}', [UsersController::class, 'update']);
    Route::delete('/users/{id}', [UsersController::class, 'destroy']);
});
