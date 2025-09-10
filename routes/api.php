<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login'])->name('login');

// Route::get('/me', [AuthController::class, 'me']);
// routes/api.php

// Ruta solo para Developer
Route::middleware(['api', 'jwt.auth', 'role:Developer'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Ruta para múltiples roles
Route::middleware(['auth:api', 'role:Developer,Admin'])->group(function () {
    // Route::get('/reports', [ReportController::class, 'index']);
    // Route::get('/analytics', [AnalyticsController::class, 'index']);
});

// Ruta pública (sin verificación de rol)
Route::middleware('auth:api')->group(function () {
    // Route::get('/profile', [ProfileController::class, 'show']);
});
