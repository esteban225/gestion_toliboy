<?php

namespace App\Console\Commands;

use App\Modules\WorkLogs\Domain\Services\WorkLogAbsenceService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyUserAbsences extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worklogs:notify-absences {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifica las ausencias de usuarios en la fecha indicada (por defecto hoy)';

    public function handle()
    {
        $date = $this->argument('date') ? Carbon::parse($this->argument('date')) : Carbon::today();
        $service = app(WorkLogAbsenceService::class);
        $service->notifyAbsencesForDate($date);
        $this->info('Notificaciones de ausencias generadas para '.$date->toDateString());
    }
}
