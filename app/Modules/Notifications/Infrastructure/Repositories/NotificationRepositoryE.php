<?php

namespace App\Modules\Notifications\Infrastructure\Repositories;

use App\Models\Notification;
use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Modules\Notifications\Domain\Repositories\NotificationRepositoryI;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class NotificationRepositoryE implements NotificationRepositoryI
{
    public function create(NotificationEntity $entity, array $userIds = []): NotificationEntity
    {
        $notification = Notification::create($entity->toArray());

        // Si es individual o grupal, asociamos usuarios
        if (! empty($userIds) && ! $notification->isGlobal()) {
            $pivotData = collect($userIds)->mapWithKeys(fn ($id) => [
                $id => ['is_read' => false, 'read_at' => null],
            ])->toArray();

            $notification->users()->syncWithoutDetaching($pivotData);
        }

        return NotificationEntity::fromArray($notification->toArray());
    }

    public function findById(int $id): ?NotificationEntity
    {
        $notification = Notification::find($id);
        if (! $notification) {
            return null;
        }

        return NotificationEntity::fromArray($notification->toArray());
    }

    public function update(NotificationEntity $entity): NotificationEntity
    {
        $notification = Notification::findOrFail($entity->getId());
        $notification->update($entity->toArray());

        return NotificationEntity::fromArray($notification->toArray());
    }

    public function delete(int $id): bool
    {
        $notification = Notification::find($id);
        if (! $notification) {
            return false;
        }

        return $notification->delete();
    }

    public function markAsRead(int $entityId, int $userId): bool
    {
        $entity = Notification::find($entityId);

        if (! $entity) {
            return false;
        }

        if ($entity->isGlobal()) {
            $entity->users()->syncWithoutDetaching([
                $userId => ['is_read' => true, 'read_at' => now()],
            ]);

            return true;
        }

        $entity->users()->updateExistingPivot($userId, [
            'is_read' => true,
            'read_at' => now(),
        ]);

        return true;
    }

    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::query()
            ->forUser($userId)
            ->notExpired()
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function getUnreadNotifications(int $userId): Collection
    {
        return Notification::query()
            ->unreadForUser($userId)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($notification) => new NotificationEntity(
                $notification->id,
                $notification->title,
                $notification->message,
                $notification->type,
                $notification->scope,
                false,
                $notification->related_table,
                $notification->related_id,
                $notification->user_id,
                $notification->expires_at
            ));
    }

    public function deleteExpiredNotifications($currentDate): int
    {
        return Notification::where('expires_at', '<', $currentDate)->delete();
    }

    public function notify(array $data, array $userIds = []): NotificationEntity
    {
        $entity = new NotificationEntity(
            null,
            $data['title'],
            $data['message'],
            $data['type'] ?? 'info',
            $data['scope'] ?? Notification::SCOPE_INDIVIDUAL,
            $data['related_table'] ?? null,
            isset($data['related_id']) ? (int) $data['related_id'] : null,
            isset($data['expires_at']) ? Carbon::parse($data['expires_at'])->timestamp : Carbon::now()->addDays((int) config('entitys.ttl_days', 2))->timestamp
        );

        return $this->create($entity, $userIds);
    }
}
