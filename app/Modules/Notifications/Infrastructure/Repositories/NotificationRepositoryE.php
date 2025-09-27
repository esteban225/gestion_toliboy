<?php

namespace App\Modules\Notifications\Infrastructure\Repositories;

use App\Modules\Notifications\Domain\Repositories\NotificationRepositoryI;
use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Models\Notification ;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class NotificationRepositoryE implements NotificationRepositoryI
{
    public function create(NotificationEntity $notification): NotificationEntity
    {
        $notificacion = Notification::create([
            'user_id' => $notification->getUserId(),
            'title' => $notification->getTitle(),
            'message' => $notification->getMessage(),
            'type' => $notification->getType(),
            'is_read' => $notification->isRead(),
            'related_table' => $notification->getRelatedTable(),
            'related_id' => $notification->getRelatedId(),
            'expires_at' => $notification->getExpiresAt(),
        ]);

        return new NotificationEntity(
            $notificacion->id,
            $notificacion->user_id,
            $notificacion->title,
            $notificacion->message,
            $notificacion->type,
            $notificacion->is_read,
            $notificacion->related_table,
            $notificacion->related_id,
            $notificacion->expires_at
        );
    }

    public function findById(int $id): ?NotificationEntity
    {
        $notificacion = Notification::find($id);
        if (! $notificacion) {
            return null;
        }

        return new NotificationEntity(
            $notificacion->id,
            $notificacion->user_id,
            $notificacion->title,
            $notificacion->message,
            $notificacion->type,
            $notificacion->is_read,
            $notificacion->related_table,
            $notificacion->related_id,
            $notificacion->expires_at
        );
    }

    public function update(NotificationEntity $notification): NotificationEntity
    {
        $notificacion = Notification::findOrFail($notification->getId());
        $notificacion->update([
            'user_id' => $notification->getUserId(),
            'title' => $notification->getTitle(),
            'message' => $notification->getMessage(),
            'type' => $notification->getType(),
            'is_read' => $notification->isRead(),
            'related_table' => $notification->getRelatedTable(),
            'related_id' => $notification->getRelatedId(),
            'expires_at' => $notification->getExpiresAt(),
        ]);

        return new NotificationEntity(
            $notificacion->id,
            $notificacion->user_id,
            $notificacion->title,
            $notificacion->message,
            $notificacion->type,
            $notificacion->is_read,
            $notificacion->related_table,
            $notificacion->related_id,
            $notificacion->expires_at
        );
    }

    public function delete(int $id): bool
    {
        $notificacion = Notification::find($id);
        if (! $notificacion) {
            return false;
        }

        return $notificacion->delete();
    }

    public function markAsRead(int $id): bool
    {
        $notificacion = Notification::find($id);
        if (! $notificacion) {
            return false;
        }
        $notificacion->is_read = true;

        return $notificacion->save();
    }

    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function getUnreadNotifications(int $userId): Collection
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($notificacion) {
                return new NotificationEntity(
                    $notificacion->id,
                    $notificacion->user_id,
                    $notificacion->title,
                    $notificacion->message,
                    $notificacion->type,
                    $notificacion->is_read,
                    $notificacion->related_table,
                    $notificacion->related_id !== null ? (int) $notificacion->related_id : null,
                    $notificacion->expires_at
                );
            });
    }
    public function deleteExpiredNotifications($currentDate): int
    {
        return Notification::where('expires_at', '<', $currentDate)->delete();
    }

    /**
     * Implementación genérica de notify.
     * Acepta NotificationEntity (si existe -> toArray()) o array payload.
     */
    public function notify(array $data): NotificationEntity
    {
        $notification = new NotificationEntity(
            null,                      // id
           (int) $data['user_id'] ?? null, // user_id
            $data['title'],           // title
            $data['message'],         // message
            $data['type'] ?? 'info',  // type
            false,                    // is_read
            $data['related_table'] ?? null, // related_table
            isset($data['related_id']) ? (int) $data['related_id'] : null   // related_id
        );

        // Usa setters si los atributos son privados
        $notification->setIsRead(false);

        return $this->create($notification);
    }

}
