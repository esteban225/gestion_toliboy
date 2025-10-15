<?php

namespace App\Modules\InventoryMovements\Infrastructure\Listeners;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Modules\InventoryMovements\Infrastructure\Events\InventoryLowStock;
use App\Modules\Notifications\Domain\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendLowStockNotification implements ShouldQueue
{
    public function handle(InventoryLowStock $event): void
    {
        $name = "ID {$event->itemId}";
        $relatedTable = 'items';

        if ($res = RawMaterial::find($event->itemId)) {
            $name = $res->name ?? $name;
            $relatedTable = 'raw_materials';
        } elseif ($res = Product::find($event->itemId)) {
            $name = $res->name ?? $name;
            $relatedTable = 'products';
        }

        $message = "El artículo {$name} tiene stock bajo ({$event->currentStock}) (umbral: {$event->threshold}).";

        // Roles destino para la notificación grupal. Puedes mover esto a un config:
        // config('notifications.low_stock_roles', ['supervisor', 'logistica'])
        $roles = ['DEV'];

        $payload = [
            'title' => "Stock bajo: {$name}",
            'message' => $message,
            'type' => 'warning',
            'related_table' => $relatedTable,
            'related_id' => $event->itemId,
        ];

        $service = app(NotificationService::class);
        try {
            // Usa el helper agregado para notificación grupal por múltiples roles
            $service->notifyGroupByRoles($roles, $payload);
        } catch (\RuntimeException|\InvalidArgumentException $e) {
            // Evita que el listener falle la cola completa; loguea para revisar
            Log::warning('[SendLowStockNotification] No se pudo enviar notificación grupal', [
                'roles' => $roles,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
