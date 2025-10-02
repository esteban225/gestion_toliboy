<?php

namespace App\Modules\InventoryMovements\Infrastructure\Events;

class InventoryLowStock
{
    public int $itemId; // raw_material_id or product_id depending your model

    public float $currentStock;

    public ?float $threshold;

    public function __construct(int $itemId, float $currentStock, ?float $threshold = null)
    {
        $this->itemId = $itemId;
        $this->currentStock = $currentStock;
        $this->threshold = $threshold;
    }
}
