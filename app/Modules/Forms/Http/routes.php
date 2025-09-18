<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Forms\Http\Controllers\FormsController;

Route::middleware(['api','jwt.auth','role:INPL,INPR,DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::apiResource('forms', FormsController::class);
});
