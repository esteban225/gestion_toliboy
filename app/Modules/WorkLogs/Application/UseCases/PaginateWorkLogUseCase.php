<?php

namespace App\Modules\WorkLogs\Application\UseCases;

use App\Modules\WorkLogs\Domain\Services\WorkLogService;
use Illuminate\Pagination\LengthAwarePaginator;

class PaginateWorkLogUseCase
{
    private WorkLogService $workLogService;

    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    public function execute(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->workLogService->paginateWorkLogs($filters, $perPage);
    }
}
