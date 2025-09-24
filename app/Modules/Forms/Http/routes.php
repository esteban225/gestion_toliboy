<?php

use App\Modules\Forms\Http\Controllers\FormReportsController;
use App\Modules\Forms\Http\Controllers\FormsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth', 'role:INPL|INPR|DEV|OP', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::apiResource('forms', FormsController::class);
});

Route::get('forms/{formId}/report/pdf', [FormReportsController::class, 'pdf']);
