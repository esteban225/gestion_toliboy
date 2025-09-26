<?php

namespace App\Modules\WorkLogs\Http;

use Illuminate\Support\Facades\Route;
use App\Modules\WorkLogs\Http\Controllers\WorkLogController;

Route::middleware(['api', 'jwt.auth'])->group(function () {
    Route::get('/work-logs', [WorkLogController::class, 'index']);
    Route::post('/work-logs', [WorkLogController::class, 'store']);
    Route::get('/work-logs/{id}', [WorkLogController::class, 'show']);
    Route::put('/work-logs/{id}', [WorkLogController::class, 'update']);
    Route::delete('/work-logs/{id}', [WorkLogController::class, 'destroy']);
    Route::get('/users/{userId}/work-logs', [WorkLogController::class, 'showUserWorkLogs']);
});
