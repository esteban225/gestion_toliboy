<?php

namespace App\Modules\Notifications\Domain\Services;

use App\Models\Notification;
use App\Models\Role; // agregado para consultas directas
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
                // Aceptar 'role' (string) o 'roles' (array de strings)
                $rolesInput = [];
                if (isset($extra['role'])) {
                    $rolesInput[] = $extra['role'];
                }
                if (isset($extra['roles']) && is_array($extra['roles'])) {
                    $rolesInput = array_merge($rolesInput, $extra['roles']);
                }
                $rolesInput = array_values(array_unique(array_filter($rolesInput)));

                if (empty($rolesInput)) {
                    throw new \InvalidArgumentException('El campo role o roles es obligatorio para notificaciones grupales');
                }

                // Obtener todos los IDs de usuarios para los roles dados
                $userIds = Role::whereIn('name', $rolesInput)
                    ->with(['users:id,role_id'])
                    ->get()
                    ->flatMap(function ($role) {
                        return $role->users->pluck('id');
                    })
                    ->unique()
                    ->values();

                if ($userIds->isEmpty()) {
                    // No hay usuarios en esos roles: puedes decidir lanzar excepción o simplemente no asociar
                    // Lanzamos excepción para que el flujo avise
                    throw new \RuntimeException('No se encontraron usuarios para los roles: '.implode(', ', $rolesInput));
                }

                $notificationModel = Notification::find($createdNotification->getId());
                $notificationModel->users()->attach($userIds, ['is_read' => false]);
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

        $entity = NotificationEntity::fromArray([
            'id' => $payload['id'] ?? null,
            'title' => $payload['title'],
            'message' => $payload['message'],
            'type' => $payload['type'] ?? null,
            'scope' => $payload['scope'] ?? null,
            'is_read' => $payload['is_read'] ?? false,
            'related_table' => $payload['related_table'] ?? null,
            'related_id' => $payload['related_id'] ?? null,
            'user_id' => $userId,
            'expires_at' => $payload['expires_at'] ?? null,
        ]);

        $notification = $this->notificationRepository->create($entity);

        // Si mandan varios user_ids, los añadimos en la pivote
        if (! empty($userIds)) {
            $notificationModel = Notification::find($notification->getId());
            $notificationModel->users()->attach($userIds, ['is_read' => false]);
        }

        return $notification;
    }

    /**
     * Helper para crear notificación grupal basada en múltiples roles.
     * $roles: array de nombres de rol
     * $payload: debe incluir al menos title, message, type (opcional), related_* (opcional)
     */
    public function notifyGroupByRoles(array $roles, array $payload)
    {
        if (empty($roles)) {
            throw new \InvalidArgumentException('Debe proporcionar al menos un rol.');
        }

        $payload['scope'] = 'group';

        // Reutilizar notify con user_ids ya resueltos
        $userIds = Role::whereIn('name', $roles)
            ->with(['users:id,role_id'])
            ->get()
            ->flatMap(fn($role) => $role->users->pluck('id'))
            ->unique()
            ->values()
            ->all();

        if (empty($userIds)) {
            throw new \RuntimeException('No se encontraron usuarios para los roles: '.implode(', ', $roles));
        }

        $payload['user_ids'] = $userIds;

        return $this->notify($payload);
    }
}
