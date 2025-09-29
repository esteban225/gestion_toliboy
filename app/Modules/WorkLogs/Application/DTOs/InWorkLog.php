<?php

namespace App\Modules\WorkLogs\Application\DTOs;

class InWorkLog
{
    public int $userId;

    public string $description;

    public \DateTime $startTime;

    public ?\DateTime $endTime;

    public function __construct(
        int $userId,
        string $description,
        \DateTime $startTime,
        ?\DateTime $endTime = null
    ) {
        $this->userId = $userId;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
    }
}
