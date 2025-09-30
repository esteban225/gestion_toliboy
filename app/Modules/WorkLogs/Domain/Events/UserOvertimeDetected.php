<?php

namespace App\Modules\WorkLogs\Domain\Events;

use Carbon\Carbon;

class UserOvertimeDetected
{
    public string $userName;

    public Carbon $date;

    public string $totalOvertime;

    public function __construct(string $userName, Carbon $date, string $totalOvertime)
    {

        $this->userName = $userName;
        $this->date = $date;
        $this->totalOvertime = $totalOvertime;
    }
}
