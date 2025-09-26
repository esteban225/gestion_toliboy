<?php

namespace App\Modules\Notifications\Domain\Services;

use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Modules\Notifications\Domain\Repositories\NotificationRepositoryI;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

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
}
