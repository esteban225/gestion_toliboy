<?php

namespace App\Modules\WorkLogs\Application\DTOs;

class WorkLogDTO
{
    public ?int $id;

    public int $user_id;

    public string $date;

    public string $start_time;

    public ?string $end_time;

    public ?string $total_hours;

    public ?string $overtime_hours;

    public ?int $batch_id;

    public ?string $task_description;

    public ?string $notes;

    public function __construct(
        ?int $id,
        int $user_id,
        string $date,
        string $start_time,
        ?string $end_time = null,
        ?string $total_hours = null,
        ?string $overtime_hours = null,
        ?int $batch_id = null,
        ?string $task_description = null,
        ?string $notes = null,
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->date = $date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->total_hours = $total_hours;
        $this->overtime_hours = $overtime_hours;
        $this->batch_id = $batch_id;
        $this->task_description = $task_description;
        $this->notes = $notes;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'total_hours' => $this->total_hours,
            'overtime_hours' => $this->overtime_hours,
            'batch_id' => $this->batch_id,
            'task_description' => $this->task_description,
            'notes' => $this->notes,
        ];
    }
}
