<?php

namespace App\Observers;

use App\Models\RawMaterial;
use App\Modules\InventoryMovements\Domain\Events\InventoryLowStock;
use Illuminate\Support\Facades\DB;

class RawMaterialObserver
{
    /**
     * Handle the RawMaterial "created" event.
     */
    public function created(RawMaterial $rawMaterial): void
    {
        //
    }

    /**
     * Handle the RawMaterial "updated" event.
     */
    public function updated(RawMaterial $rawMaterial): void
    {
        if (! $rawMaterial->isDirty('stock')) {
            return;
        }

        $stock = (float) $rawMaterial->stock;
        $threshold = $rawMaterial->min_stock !== null ? (float) $rawMaterial->min_stock : null;

        if ($threshold === null) {
            return; // sin umbral no hay notificación
        }

        if ($stock <= $threshold) {
            // Opcional: evitar duplicados recientes (consulta rápida)
            $exists = \App\Models\Notification::where('type', 'low_stock')
                ->where('related_table', 'raw_materials')
                ->where('related_id', $rawMaterial->id)
                ->where('created_at', '>=', now()->subMinutes(60))
                ->exists();

            if ($exists) {
                return;
            }

            // Después de commit (si hay transacción)
            if (DB::transactionLevel() > 0) {
                DB::afterCommit(function () use ($rawMaterial, $stock, $threshold) {
                    event(new InventoryLowStock($rawMaterial->id, (int) $stock, (int) $threshold));
                });
            } else {
                event(new InventoryLowStock($rawMaterial->id, (int) $stock, (int) $threshold));
            }
        }
    }

    /**
     * Handle the RawMaterial "deleted" event.
     */
    public function deleted(RawMaterial $rawMaterial): void
    {
        //
    }

    /**
     * Handle the RawMaterial "restored" event.
     */
    public function restored(RawMaterial $rawMaterial): void
    {
        //
    }

    /**
     * Handle the RawMaterial "force deleted" event.
     */
    public function forceDeleted(RawMaterial $rawMaterial): void
    {
        //
    }
}
