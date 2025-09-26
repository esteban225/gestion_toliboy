<?php

namespace App\Modules\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Notifications\Application\UseCases\NotificationUseCase;
use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Modules\Notifications\Http\Requests\RegisterRequest;
use App\Modules\Notifications\Http\Requests\UpdateRequest;
use App\Modules\Notifications\Http\Resources\NotificationResource;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    private NotificationUseCase $notificationUseCase;

    public function __construct(NotificationUseCase $notificationUseCase)
    {
        $this->notificationUseCase = $notificationUseCase;
    }

    /**
     * Crear notificación
     */
    public function createNotification(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $notification = new NotificationEntity(
            null,
            $data['user_id'],
            $data['title'],
            $data['message'],
            $data['type'],
            false,
            $data['related_table'] ?? null,
            $data['related_id'] ?? null,
            isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null
        );

        $createdNotification = $this->notificationUseCase->createNotification($notification);

        return response()->json(new NotificationResource($createdNotification), 201);
    }

    /**
     * Obtener notificación por ID
     */
    public function getNotificationById(int $id): JsonResponse
    {
        $notification = $this->notificationUseCase->getNotificationById($id);

        if (! $notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json(new NotificationResource($notification));
    }

    /**
     * Actualizar notificación
     */
    public function updateNotification(UpdateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $notification = $this->notificationUseCase->getNotificationById($id);

        if (! $notification) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        // Clonar datos existentes y aplicar solo los cambios
        $notification = new NotificationEntity(
            $notification->getId(),
            $data['user_id'] ?? $notification->getUserId(),
            $data['title'] ?? $notification->getTitle(),
            $data['message'] ?? $notification->getMessage(),
            $data['type'] ?? $notification->getType(),
            $data['is_read'] ?? $notification->isRead(),
            $data['related_table'] ?? $notification->getRelatedTable(),
            $data['related_id'] ?? $notification->getRelatedId(),
            isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : $notification->getExpiresAt()
        );

        $updatedNotification = $this->notificationUseCase->updateNotification($notification);

        return response()->json(new NotificationResource($updatedNotification));
    }

    /**
     * Eliminar notificación
     */
    public function deleteNotification(int $id): JsonResponse
    {
        $deleted = $this->notificationUseCase->deleteNotification($id);

        if (! $deleted) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json(['message' => 'Notification deleted']);
    }

    /**
     * Marcar como leída
     */
    public function markAsRead(int $id): JsonResponse
    {
        $marked = $this->notificationUseCase->markAsRead($id);

        if (! $marked) {
            return response()->json(['message' => 'Notification not found'], 404);
        }

        return response()->json(['message' => 'Notification marked as read']);
    }

    /**
     * Listar notificaciones del usuario autenticado
     */
    public function getUserNotifications(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $perPage = $request->query('per_page', 15);

        $notifications = $this->notificationUseCase->getUserNotifications($userId, $perPage);

        return response()->json(NotificationResource::collection($notifications));
    }

    /**
     * Listar notificaciones no leídas
     */
    public function getUnreadNotifications(): JsonResponse
    {
        $userId = Auth::id();
        $notifications = $this->notificationUseCase->getUnreadNotifications($userId);

        return response()->json(NotificationResource::collection($notifications));
    }

    /**
     * Eliminar notificaciones expiradas
     */
    public function deleteExpiredNotifications(): JsonResponse
    {
        $currentDate = Carbon::now();
        $deletedCount = $this->notificationUseCase->deleteExpiredNotifications($currentDate);

        return response()->json(['deleted_count' => $deletedCount]);
    }
}
