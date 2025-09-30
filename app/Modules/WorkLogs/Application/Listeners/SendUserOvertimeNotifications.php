<?php

namespace App\Modules\WorkLogs\Application\Listeners;

use App\Modules\Notifications\Domain\Services\NotificationService;
use App\Modules\WorkLogs\Domain\Events\UserOvertimeDetected;

class SendUserOvertimeNotifications
{
    /**
     * Maneja el evento y crea una notificación usando NotificationService.
     */
    public function handle(UserOvertimeDetected $event): void
    {
        /** @var NotificationService $ns */
        $ns = app(NotificationService::class);
        $ns->notify([
            'user_id' => null,
            'title' => 'Horas extra detectadas',
            'message' => "El usuario {$event->userName} ha registrado {$event->totalOvertime} horas extra el día {$event->date->format('d/m/Y')}.",
            'type' => 'info',
            'related_table' => 'users',
            'related_id' => null,
        ]);
    }
}
