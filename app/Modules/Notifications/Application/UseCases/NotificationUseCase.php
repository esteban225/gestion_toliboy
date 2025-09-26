<?php


namespace App\Modules\Notifications\Application\UseCases;
use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use App\Modules\Notifications\Domain\Services\NotificationService;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class NotificationUseCase
{
    private NotificationService $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function createNotification(NotificationEntity $notification): NotificationEntity
    {
        return $this->notificationService->createNotification($notification);
    }

    public function getNotificationById(int $id): ?NotificationEntity
    {
        return $this->notificationService->getNotificationById($id);
    }

    public function updateNotification(NotificationEntity $notification): NotificationEntity
    {
        return $this->notificationService->updateNotification($notification);
    }

    public function deleteNotification(int $id): bool
    {
        return $this->notificationService->deleteNotification($id);
    }

    public function markAsRead(int $id): bool
    {
        return $this->notificationService->markAsRead($id);
    }

    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->notificationService->getUserNotifications($userId, $perPage);
    }

    public function getUnreadNotifications(int $userId): Collection
    {
        return $this->notificationService->getUnreadNotifications($userId);
    }

    public function deleteExpiredNotifications(Carbon $currentDate): int
    {
        return $this->notificationService->deleteExpiredNotifications($currentDate);
    }
}
