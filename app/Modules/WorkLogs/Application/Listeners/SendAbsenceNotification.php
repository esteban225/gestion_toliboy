<?php

namespace App\Modules\WorkLogs\Application\Listeners;

use App\Modules\Notifications\Domain\Services\NotificationService;
use App\Modules\WorkLogs\Domain\Events\UserAbsenceDetected;

class SendAbsenceNotification
{
    /**
     * Maneja el evento y crea una notificación usando NotificationService.
     */
    public function handle(UserAbsenceDetected $event): void
    {
        /** @var NotificationService $ns */
        $ns = app(NotificationService::class);
        $ns->notify([
            'user_id' => $event->userId,
            'title' => 'Ausencia detectada',
            'message' => "El usuario {$event->userName} no asistió el día {$event->date->format('d/m/Y')}.",
            'type' => 'warning',
            'related_table' => 'users',
            'related_id' => $event->userId,
        ]);
    }
}
