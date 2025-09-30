<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        \App\Modules\InventoryMovements\Domain\Events\InventoryLowStock::class => [
            \App\Modules\Notifications\Application\Listeners\SendLowStockNotification::class,
        ],
        // Notificación de ausencia de usuario
        \App\Modules\WorkLogs\Domain\Events\UserAbsenceDetected::class => [
            \App\Modules\WorkLogs\Application\Listeners\SendAbsenceNotification::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
