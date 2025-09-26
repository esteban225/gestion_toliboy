<?php

namespace App\Modules\WorkLogs\Domain\Services;

use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Domain\Repositories\WorkLogRepositoryI;

class WorkLogService
{
    private WorkLogRepositoryI $workLogRepository;

    public function __construct(WorkLogRepositoryI $workLogRepository)
    {
        $this->workLogRepository = $workLogRepository;
    }

    public function createWorkLog(WorkLogEntity $workLog): WorkLogEntity
    {
        return $this->workLogRepository->create($workLog);
    }

    public function updateWorkLog(WorkLogEntity $workLog): WorkLogEntity
    {
        return $this->workLogRepository->update($workLog);
    }

    public function deleteWorkLog(int $id): bool
    {
        return $this->workLogRepository->delete($id);
    }

    public function getWorkLogById(int $id): ?WorkLogEntity
    {
        return $this->workLogRepository->findById($id);
    }

    public function getWorkLogsByUserId(int $userId): array
    {
        return $this->workLogRepository->findByUserId($userId);
    }
    public function getAllWorkLogs(): array
    {
        return $this->workLogRepository->findAll();
    }
}
