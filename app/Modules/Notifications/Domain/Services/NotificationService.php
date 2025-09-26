<?php

namespace App\Modules\Notifications\Domain\Services;

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

    public function createNotification(NotificationEntity $notification): NotificationEntity
    {
        return $this->notificationRepository->create($notification);
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

    public function markAsRead(int $id): bool
    {
        return $this->notificationRepository->markAsRead($id);
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
        // Normalizar user_id a int|null para cumplir la firma ?int del constructor
        $userId = null;
        if (array_key_exists('user_id', $payload) && $payload['user_id'] !== '' && $payload['user_id'] !== null) {
            $userId = is_int($payload['user_id']) ? $payload['user_id'] : (int) $payload['user_id'];
        }

        // Normalizar related_id a int|null
        $relatedId = null;
        if (array_key_exists('related_id', $payload) && $payload['related_id'] !== '' && $payload['related_id'] !== null) {
            $relatedId = is_int($payload['related_id']) ? $payload['related_id'] : (int) $payload['related_id'];
        }

        $expiresAt = $payload['expires_at'] ?? null;
        if ($expiresAt !== null && $expiresAt !== '') {
            $expiresAt = \Carbon\Carbon::parse($expiresAt);
        } else {
            $expiresAt = null;
        }

        $entity = new \App\Modules\Notifications\Domain\Entities\NotificationEntity(
            $payload['id'] ?? null,
            $userId,
            $payload['title'] ?? '',
            $payload['message'] ?? '',
            $payload['type'] ?? null,
            $payload['is_read'] ?? false,
            $payload['related_table'] ?? null,
            $relatedId,
            $expiresAt
        );

        return $this->notificationRepository->create($entity);
    }
}
