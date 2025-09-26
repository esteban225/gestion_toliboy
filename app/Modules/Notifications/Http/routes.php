<?php

use App\Modules\Notifications\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::prefix('notifications')->middleware(['api', 'jwt.auth'])->group(function () {

    // 📋 GET - Obtener notificaciones del usuario autenticado (con paginación)
    Route::get('/', [NotificationController::class, 'getUserNotifications'])
        ->name('notifications.user');

    // 🔔 GET - Obtener notificaciones NO leídas del usuario
    Route::get('/unread', [NotificationController::class, 'getUnreadNotifications'])
        ->name('notifications.unread');

    // 📄 GET - Obtener una notificación específica por ID
    Route::get('/{id}', [NotificationController::class, 'getNotificationById'])
        ->where('id', '[0-9]+')
        ->name('notifications.show');

    // ➕ POST - Crear una nueva notificación
    Route::post('/', [NotificationController::class, 'createNotification'])
        ->name('notifications.create');

    // ✏️ PUT - Actualizar una notificación completa
    Route::put('/{id}', [NotificationController::class, 'updateNotification'])
        ->where('id', '[0-9]+')
        ->name('notifications.update');

    // ✅ PATCH - Marcar como leída (actualización parcial)
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])
        ->where('id', '[0-9]+')
        ->name('notifications.mark-as-read');

    // 🗑️ DELETE - Eliminar una notificación específica
    Route::delete('/{id}', [NotificationController::class, 'deleteNotification'])
        ->where('id', '[0-9]+')
        ->name('notifications.delete');

    // 🧹 DELETE - Eliminar notificaciones expiradas (acción administrativa)
    Route::delete('/actions/clean-expired', [NotificationController::class, 'deleteExpiredNotifications'])
        ->name('notifications.clean-expired');
});

// 🔄 Alternativa si prefieres rutas resource (más concisas)
// Route::prefix('api')->middleware(['api'])->group(function () {
//     Route::apiResource('notifications', NotificationController::class)->except(['store']);

//     // Rutas adicionales que no vienen con apiResource
//     Route::get('/notifications/user/unread', [NotificationController::class, 'getUnreadNotifications'])
//         ->name('notifications.unread');

//     Route::post('/notifications', [NotificationController::class, 'createNotification'])
//         ->name('notifications.store');

//     Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])
//         ->name('notifications.mark-as-read');

//     Route::delete('/notifications/actions/clean-expired', [NotificationController::class, 'deleteExpiredNotifications'])
//         ->name('notifications.clean-expired');
// });
