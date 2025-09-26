<?php

namespace App\Modules\WorkLogs\Application\UseCases;

use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Domain\Services\WorkLogService;

class WorkLogUseCase
{
    private WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function createWorkLog(WorkLogEntity $workLog): WorkLogEntity
    {
        $workLogModel = $this->workLogService->createWorkLog($workLog);
        // Aquí puedes llamar al servicio de notificaciones si es necesario
        return $workLogModel;
    }

    public function updateWorkLog(WorkLogEntity $workLog): WorkLogEntity
    {
        $workLogModel = $this->workLogService->updateWorkLog($workLog);
        // Aquí puedes llamar al servicio de notificaciones si es necesario
        return $workLogModel;
    }

    public function deleteWorkLog(int $id): bool
    {
        $result = $this->workLogService->deleteWorkLog($id);
        // Aquí puedes llamar al servicio de notificaciones si es necesario
        return $result;
    }

    public function getWorkLogById(int $id): ?WorkLogEntity
    {
        return $this->workLogService->getWorkLogById($id);
    }

    public function getWorkLogsByUserId(int $userId): array
    {
        return $this->workLogService->getWorkLogsByUserId($userId);
    }
    public function getAllWorkLogs(): array
    {
        return $this->workLogService->getAllWorkLogs();
    }
}
