<?php

namespace App\Modules\WorkLogs\Domain\Entities;


class WorkLogEntity
{
    public function __construct(
        public ?int $id,
        public int $user_id,
        public ?string $date,
        public ?string $start_time,
        public ?string $end_time,
        public ?string $total_hours,
        public ?string $overtime_hours,
        public ?string $batch_id,
        public ?string $task_description,
        public ?string $notes,
    ) {}
}
