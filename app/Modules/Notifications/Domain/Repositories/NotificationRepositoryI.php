<?php

namespace App\Modules\Notifications\Domain\Repositories;

use App\Modules\Notifications\Domain\Entities\NotificationEntity;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

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

    public function notify(array $data): NotificationEntity;
}
