<?php

namespace App\Modules\WorkLogs\Domain\Services;

use App\Models\User;
use App\Modules\WorkLogs\Domain\Events\UserAbsenceDetected;
use App\Modules\WorkLogs\Domain\Events\UserOvertimeDetected;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class WorkLogAbsenceService
{
    /**
     * Detecta usuarios sin registro de asistencia en una fecha y dispara el evento de ausencia.
     *
     * @param  Carbon  $date  Fecha a verificar (por defecto hoy)
     */
    public function notifyAbsencesForDate(Carbon $date): void
    {
        $date = $date ?? Carbon::today();
        $users = User::all();

        foreach ($users as $user) {
            $hasWorkLog = $user->work_logs()
                ->whereDate('date', $date->toDateString())
                ->exists();

            if (! $hasWorkLog) {
                Event::dispatch(new UserAbsenceDetected($user->id, $user->name, $date));
            }
        }
    }

    public function notifyBusinessDay(Carbon $date): void
    {

        $users = User::with('work_logs')->get();

        foreach ($users as $user) {

            $totalOvertime = $user->work_logs->where('date', $date->toDateString())->sum('overtime_hours');

            if ($totalOvertime > 24) {
                Event::dispatch(new UserOvertimeDetected($user->name, $date, $totalOvertime));
            }

        }

    }
}
