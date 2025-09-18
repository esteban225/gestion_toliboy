<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Notifications\Http\Controllers\NotificationsController;

Route::middleware(['api','jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('notifications', [NotificationsController::class,'index']);
    Route::get('notifications/summary', [NotificationsController::class,'summary']);
});
