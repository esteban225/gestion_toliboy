<?php

// filepath: c:\Users\USUARIO\Desktop\TOLIBOY\PROYECTOS\backend-toliboy\gestion_toliboy\app\Modules\Reports\Http\routes.php

use App\Modules\Reports\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::prefix('reports')->middleware(['api', 'jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('/{reportName}', [ReportsController::class, 'report']);
    Route::get('/{reportName}/export', [ReportsController::class, 'export']);
    Route::post('/export', [ReportsController::class, 'exportReport']);
});
