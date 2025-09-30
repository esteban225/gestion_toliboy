<?php

namespace App\Modules\Notifications\Domain\Services;

use App\Models\Notification;
use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Modules\Notifications\Domain\Repositories\NotificationRepositoryI;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationService
{
    private NotificationRepositoryI $notificationRepository;

    public function __construct(NotificationRepositoryI $notificationRepository)
    {
        $this->notificationRepository = $notificationRepository;
    }

    public function createNotification(NotificationEntity $notification, array $extra = []): NotificationEntity
    {
        // Si no trae scope, por defecto individual
        if ($notification->getScope() === null) {
            $notification->setScope('individual');
        }

        // Si no trae fecha de expiración, asignamos 2 días por defecto
        if ($notification->getExpiresAt() === null) {
            $notification->setExpiresAt(Carbon::now()->addDays(2));
        }

        // Crear notificación en BD
        $createdNotification = $this->notificationRepository->create($notification);

        // Lógica según scope
        switch ($notification->getScope()) {
            case 'individual':
                if (! isset($extra['user_id'])) {
                    throw new \InvalidArgumentException('El campo user_id es obligatorio para notificaciones individuales');
                }

                $notificationModel = Notification::find($createdNotification->getId());
                $notificationModel->users()->attach($extra['user_id']);
                break;

            case 'group':
                if (! isset($extra['role'])) {
                    throw new \InvalidArgumentException('El campo role es obligatorio para notificaciones grupales');
                }

                // 1. Encuentra el Modelo de Rol que te interesa
                $role = \App\Models\Role::where('name', $extra['role'])->first();

                if ($role) {
                    // 2. Consulta la relación 'users' en ese modelo de Rol y extrae los IDs
                    $userIds = $role->users()->pluck('id');

                    // 3. Continúa con tu lógica original
                    $notificationModel = Notification::find($createdNotification->getId());
                    $notificationModel->users()->attach($userIds);
                }
                break;

            case 'global':
                // Global no requiere usuarios, todos la verán
                break;

            default:
                throw new \InvalidArgumentException('Scope inválido: '.$notification->getScope());
        }

        return $createdNotification;
    }

    public function getNotificationById(int $id): ?NotificationEntity
    {
        return $this->notificationRepository->findById($id);
    }

    public function updateNotification(NotificationEntity $notification): NotificationEntity
    {
        return $this->notificationRepository->update($notification);
    }

    public function deleteNotification(int $id): bool
    {
        return $this->notificationRepository->delete($id);
    }

    public function markAsRead(int $notificationId, int $userId): bool
    {
        return $this->notificationRepository->markAsRead($notificationId, $userId);
    }

    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->notificationRepository->getUserNotifications($userId, $perPage);
    }

    public function getUnreadNotifications(int $userId): Collection
    {
        return $this->notificationRepository->getUnreadNotifications($userId);
    }

    public function deleteExpiredNotifications(Carbon $currentDate): int
    {
        return $this->notificationRepository->deleteExpiredNotifications($currentDate);
    }

    public function notify(array $payload)
    {
        $userIds = $payload['user_ids'] ?? [];
        $userId = $payload['user_id'] ?? null;

        $entity = new NotificationEntity(
            $payload['id'] ?? null,
            $userId,
            $payload['title'] ?? '',
            $payload['message'] ?? '',
            $payload['type'] ?? null,
            $payload['is_read'] ?? false,
            $payload['related_table'] ?? null,
            $payload['related_id'] ?? null,
            $payload['expires_at'] ?? null
        );

        $notification = $this->notificationRepository->create($entity);

        // Si mandan varios user_ids, los añadimos en la pivote
        if (! empty($userIds)) {
            $notificationModel = Notification::find($notification->getId());
            $notificationModel->users()->attach($userIds, ['is_read' => false]);
        }

        return $notification;
    }
}
