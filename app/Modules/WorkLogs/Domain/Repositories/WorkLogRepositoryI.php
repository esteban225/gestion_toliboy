<?php

namespace App\Modules\WorkLogs\Domain\Repositories;

use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;

interface WorkLogRepositoryI
{
    public function create(WorkLogEntity $workLog): WorkLogEntity;
    public function update(WorkLogEntity $workLog): WorkLogEntity;
    public function delete(int $id): bool;
    public function findById(int $id): ?WorkLogEntity;
    public function findByUserId(int $userId): array;
    public function findAll(): array;
}
