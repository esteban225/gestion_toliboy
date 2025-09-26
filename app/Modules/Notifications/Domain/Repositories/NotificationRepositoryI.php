<?php

namespace App\Modules\Notifications\Domain\Repositories;

use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;
interface NotificationRepositoryI
{
    public function create(NotificationEntity $notification): NotificationEntity;
    public function findById(int $id): ?NotificationEntity;
    public function update(NotificationEntity $notification): NotificationEntity;
    public function delete(int $id): bool;
    public function markAsRead(int $id): bool;
    public function getUserNotifications(int $userId, int $perPage = 15): LengthAwarePaginator;
    public function getUnreadNotifications(int $userId): Collection;
    public function deleteExpiredNotifications(Carbon $currentDate): int;
}
