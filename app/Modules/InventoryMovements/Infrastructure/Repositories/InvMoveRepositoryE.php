<?php

namespace App\Modules\InventoryMovements\Infrastructure\Repositories;

use App\Models\InventoryMovement;
use App\Models\RawMaterial;
use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;
use App\Modules\InventoryMovements\Domain\Repositories\InvMoveRepositoryI;
use App\Modules\InventoryMovements\Infrastructure\Events\InventoryLowStock ;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class InvMoveRepositoryE implements InvMoveRepositoryI
{
    public function list(array $filters = [], int $perpage = 15): LengthAwarePaginator
    {
        $query = InventoryMovement::query();

        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        return $query->paginate($perpage);
    }

    public function find(int $id): ?InvMoveEntity
    {
        $response = InventoryMovement::find($id);

        return InvMoveEntity::fromModel($response);
    }

    public function create(InvMoveEntity $entity): InvMoveEntity
    {
        $InventoryMovement = InventoryMovement::create($entity->toArray());

        return InvMoveEntity::fromModel($InventoryMovement);
    }

    public function update(InvMoveEntity $entity): bool
    {
        $InventoryMovement = InventoryMovement::find($entity->getId());

        if (! $InventoryMovement) {
            return false;
        }

        $InventoryMovement->update($entity->toArray());

        return true;
    }

    public function delete(int $id): bool
    {
        $InventoryMovement = InventoryMovement::find($id);

        if (! $InventoryMovement) {
            return false;
        }

        $InventoryMovement->delete();

        return true;
    }

    public function reduceStock(int $itemId, float $qty): void
    {
        DB::transaction(function () use ($itemId, $qty, &$newStock, &$threshold) {
            // Bloquea el registro para evitar condiciones de carrera
            $item = RawMaterial::lockForUpdate()->findOrFail($itemId);

            // Calcula nuevo stock (respeta los decimales)
            $newStock = max(0, (float) $item->stock - $qty);
            $item->stock = $newStock;
            $item->save();

            // Usa min_stock como umbral (o config por defecto si está NULL)
            $threshold = $item->min_stock ?? config('inventory.default_low_stock_threshold', 10);
        });

        // Dispara evento solo si se llegó al umbral
        if ($threshold !== null && $newStock <= $threshold) {
            event(new InventoryLowStock($itemId, $newStock, $threshold));
        }
    }
}
