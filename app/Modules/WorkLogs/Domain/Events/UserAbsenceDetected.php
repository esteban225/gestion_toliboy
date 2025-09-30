<?php

namespace App\Modules\WorkLogs\Domain\Events;

use Carbon\Carbon;

class UserAbsenceDetected
{
    public int $userId;

    public string $userName;

    public Carbon $date;

    public function __construct(int $userId, string $userName, Carbon $date)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->date = $date;
    }
}
