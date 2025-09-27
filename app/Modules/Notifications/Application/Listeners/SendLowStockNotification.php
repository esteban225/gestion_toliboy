<?php

namespace App\Modules\Notifications\Application\Listeners;

use App\Models\Product;
use App\Models\RawMaterial;
use App\Modules\InventoryMovements\Domain\Events\InventoryLowStock;
use App\Modules\Notifications\Domain\Services\NotificationService;

class SendLowStockNotification
{
    /**
     * Maneja el evento y crea una notificación usando NotificationService.
     */
    public function handle(InventoryLowStock $event): void
    {
        // Intentar resolver nombre del recurso (RawMaterial o Product)
        $name = null;
        $relatedTable = 'items';

        if ($res = RawMaterial::find($event->itemId)) {
            $name = $res->name ?? "ID {$event->itemId}";
            $relatedTable = 'raw_materials';
        } elseif ($res = Product::find($event->itemId)) {
            $name = $res->name ?? "ID {$event->itemId}";
            $relatedTable = 'products';
        } else {
            $name = "ID {$event->itemId}";
        }

        $message = "El artículo {$name} tiene stock bajo ({$event->currentStock} unidades).";

        // Notificar (user_id null = notificación genérica; puedes pasar id del responsable)
        /** @var NotificationService $ns */
        $ns = app(NotificationService::class);
        $ns->notify([
            'user_id' => null,
            'title' => "Stock bajo: {$name}",
            'message' => $message,
            'type' => 'warning',
            'related_table' => $relatedTable,
            'related_id' => $event->itemId,
        ]);
    }
}
