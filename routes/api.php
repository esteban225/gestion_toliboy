<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RawMaterialsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\BatchesController;
use App\Http\Controllers\InventoryMovementsController;
use App\Http\Controllers\FormsController;
use App\Http\Controllers\FormsFilesController;
use App\Http\Controllers\FormResponseController;
use App\Http\Controllers\FormResponseValueController;
use App\Http\Controllers\WorkLogsController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');



// Rutas protegidas solo para Developer
Route::middleware(['api', 'jwt.auth', 'role:Developer', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);

    // Rutas de roles
    Route::resource('roles', RoleController::class);

    // Rutas de datos de usuario
    Route::resource('userData', UserDataController::class);

    // Rutas de gestión de usuarios
    Route::resource('users', UserController::class);

    // Rutas de materias primas
    Route::resource('raw-materials', RawMaterialsController::class);

    // Rutas de productos
    Route::resource('products', ProductsController::class);

    // Rutas de lotes
    Route::resource('batches', BatchesController::class);

    // Rutas de movimientos de inventario
    Route::resource('inventory-movements', InventoryMovementsController::class);

    // Rutas de formularios
    Route::resource('forms', FormsController::class);

    // Rutas de campos de formularios
    Route::resource('form-fields', FormsFilesController::class);

    // Rutas de respuestas de formularios
    Route::resource('form-responses', FormResponseController::class);

    // Rutas de valores de respuestas de formularios
    Route::resource('form-response-values', FormResponseValueController::class);

    // Rutas de registros de trabajo
    Route::resource('work-logs', WorkLogsController::class);

    // Rutas de notificaciones
    Route::resource('notifications', NotificationsController::class);
});

// // Ruta para múltiples roles
// Route::middleware(['api', 'role:Developer,Admin'])->group(function () {
//     // Route::get('/reports', [ReportController::class, 'index']);
//     // Route::get('/analytics', [AnalyticsController::class, 'index']);
// });

// // Ruta pública (sin verificación de rol)
// Route::middleware('api')->group(function () {
//     // Route::get('/profile', [ProfileController::class, 'show']);
// });
