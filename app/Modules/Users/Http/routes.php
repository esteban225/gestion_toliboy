<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Users\Http\Controllers\UsersController;

Route::middleware(['api','jwt.auth','role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::apiResource('module/users', UsersController::class);
});
