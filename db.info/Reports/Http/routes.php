<?php

// filepath: c:\Users\USUARIO\Desktop\TOLIBOY\PROYECTOS\backend-toliboy\gestion_toliboy\app\Modules\Reports\Http\routes.php

use App\Modules\Reports\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth', 'role:GG,INPL,INPR,DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('reports/{reportName}', [ReportsController::class, 'report']);
    Route::get('reports/{reportName}/export', [ReportsController::class, 'export']);
});
