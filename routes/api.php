<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BatchesController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\FormsFilesController;
use App\Http\Controllers\FormResponseController;
use App\Http\Controllers\FormResponseValueController;
use App\Http\Controllers\InventoryMovementsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\RawMaterialsController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\ViewsController;
use App\Http\Controllers\WorkLogsController;
use App\Http\Controllers\ReportsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public auth
|--------------------------------------------------------------------------
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login'])->name('login');

/*
|--------------------------------------------------------------------------
| Authenticated common routes (any authenticated user)
|--------------------------------------------------------------------------
*/
Route::middleware(['api', 'jwt.auth', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Notificaciones básicas
    Route::get('notifications', [NotificationsController::class, 'index']);
});

// /*
// |--------------------------------------------------------------------------
// | DEV - Developer (administración total)
// |--------------------------------------------------------------------------
// */
// Route::middleware(['api', 'jwt.auth', 'role:DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
//     Route::apiResource('roles', RoleController::class);
//     Route::apiResource('users', UserController::class);
//     Route::apiResource('userData', UserDataController::class);

//     Route::apiResource('raw-materials', RawMaterialsController::class);
//     Route::apiResource('products', ProductsController::class);
//     Route::apiResource('batches', BatchesController::class);
//     Route::apiResource('inventory-movements', InventoryMovementsController::class);

//     Route::apiResource('forms', FormsController::class);
//     Route::apiResource('form-fields', FormsFilesController::class);
//     Route::apiResource('form-responses', FormResponseController::class);
//     Route::apiResource('form-response-values', FormResponseValueController::class);

//     Route::apiResource('work-logs', WorkLogsController::class);
//     Route::apiResource('notifications', NotificationsController::class);

//     // Vistas / dashboard
//     Route::get('views', [ViewsController::class, 'index']);
//     Route::get('dashboard', [ViewsController::class, 'dashboard']);
//     Route::get('views/{view}', [ViewsController::class, 'show']);
// });

// /*
// |--------------------------------------------------------------------------
// | GG - Gerente General (estadísticas, dashboards, notificaciones)
// |--------------------------------------------------------------------------
// */
// Route::middleware(['api', 'jwt.auth', 'role:GG', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
//     Route::get('dashboard', [ViewsController::class, 'dashboard']);
//     Route::get('views/{view}', [ViewsController::class, 'show']);

//     // Notificaciones para widgets/front
//     Route::get('notifications/summary', [NotificationsController::class, 'summary']);
// });

// /*
// |--------------------------------------------------------------------------
// | INPL / INPR - Ingenieros (estadísticas, formularios, work-logs)
// |--------------------------------------------------------------------------
// */
// Route::middleware(['api', 'jwt.auth', 'role:INPL|INPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
//     Route::get('dashboard', [ViewsController::class, 'dashboard']);
//     Route::get('views/{view}', [ViewsController::class, 'show']);

//     // Formularios (solo ver y responder)
//     Route::apiResource('forms', FormsController::class)->only(['index', 'show']);
//     Route::apiResource('form-responses', FormResponseController::class);

//     // Work logs (ingenieros)
//     Route::apiResource('work-logs', WorkLogsController::class)->only(['index', 'show', 'store', 'update']);
// });

// /*
// |--------------------------------------------------------------------------
// | TRZ - Trazabilidad (informes, lectura de formularios)
// |--------------------------------------------------------------------------
// */
// Route::middleware(['api', 'jwt.auth', 'role:TRZ', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
//     Route::apiResource('batches', BatchesController::class)->only(['index', 'show']);
//     Route::apiResource('form-responses', FormResponseController::class)->only(['index', 'show']);
// });

// /*
// |--------------------------------------------------------------------------
// | OP - Operario (diligenciamiento de formularios y registro de horas)
// |--------------------------------------------------------------------------
// */
// Route::middleware(['api', 'jwt.auth', 'role:OP', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
//     // Reports accesibles a operarios si corresponde (ajusta roles si no)
//     Route::get('reports/{reportName}', [ReportsController::class, 'report']);
//     Route::get('reports/{reportName}/export', [ReportsController::class, 'export']);

//     // Form responses (operarios)
//     Route::post('form-responses', [FormResponseValueController::class, 'store']); // crear respuesta / valores

//     // Work logs (operarios)
//     Route::post('work-logs', [WorkLogsController::class, 'store']); // fichadas operario
// });

/*
|--------------------------------------------------------------------------
| Load extra module routes
|--------------------------------------------------------------------------
*/
$moduleRouteFiles = glob(base_path('app/Modules/*/Http/routes.php'));
if ($moduleRouteFiles !== false) {
    foreach ($moduleRouteFiles as $file) {
        require $file;
    }
}
