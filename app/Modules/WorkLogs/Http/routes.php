<?php

use App\Modules\WorkLogs\Http\Controllers\WorkLogsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth', 'role:OP,INPL,INPR,DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('work-logs', [WorkLogsController::class, 'index']);
    Route::post('work-logs', [WorkLogsController::class, 'store']);
    Route::get('work-logs/{id}', [WorkLogsController::class, 'show']);
});
