<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Roles\Http\Controllers\RolesController;

Route::middleware(['api','jwt.auth','role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::apiResource('roles', RolesController::class)->only(['index','store','update']);
});
