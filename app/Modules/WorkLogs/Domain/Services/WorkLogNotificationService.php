<?php

namespace App\Modules\WorkLogs\Domain\Services;

use App\Models\WorkLog;
use App\Models\Notification;
use App\Models\User;

use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;


class WorkLogNotificationService
{

    public function notifyEntry(WorkLogEntity $workLog): void
    {
        $workLogModel = WorkLog::find($workLog->id);
        if ($workLogModel) {
            // Aquí puedes implementar la lógica para enviar una notificación
            // Por ejemplo, enviar un correo electrónico o una notificación en la aplicación
        }

    }
    public function notifyWorkLogCreated(WorkLogEntity $workLog): void
    {
        // Lógica para notificar la creación de un WorkLog
        // Por ejemplo, enviar un correo electrónico o una notificación en la aplicación
    }

    public function notifyWorkLogUpdated(WorkLogEntity $workLog): void
    {
        // Lógica para notificar la actualización de un WorkLog
    }

    public function notifyWorkLogDeleted(int $workLogId): void
    {
        // Lógica para notificar la eliminación de un WorkLog
    }
}
