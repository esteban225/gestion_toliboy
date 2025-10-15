<?php

namespace App\Providers;

use App\Modules\InventoryMovements\Infrastructure\Events\InventoryLowStock;
use App\Modules\InventoryMovements\Infrastructure\Listeners\SendLowStockNotification;
use App\Modules\WorkLogs\Application\Listeners\SendAbsenceNotification;
use App\Modules\WorkLogs\Domain\Events\UserAbsenceDetected;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        InventoryLowStock::class => [
            SendLowStockNotification::class,
        ],
        // Notificación de ausencia de usuario
        UserAbsenceDetected::class => [
            SendAbsenceNotification::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
