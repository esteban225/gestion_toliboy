<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Forms\Http\Controllers\FormsController;
use App\Modules\Forms\Http\Controllers\FormReportsController;

Route::middleware(['api', 'jwt.auth', 'role:INPL|INPR|DEV|OP', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::apiResource('forms', FormsController::class);
});

    Route::get('forms/{formId}/report/pdf', [FormReportsController::class, 'pdf']);

