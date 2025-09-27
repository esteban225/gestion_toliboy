<?php

namespace App\Modules\InventoryMovements\Infrastructure\Repositories;

use App\Models\InventoryMovement;
use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;
use App\Modules\InventoryMovements\Domain\Repositories\InvMoveRepositoryI;

class InvMoveRepositoryE implements InvMoveRepositoryI
{
    public function list(array $filters = []): array
    {
        $query = InventoryMovement::query();

        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get()->map(function ($InventoryMovement) {
            return new InvMoveEntity(
                $InventoryMovement->id,
                $InventoryMovement->raw_material_id,
                $InventoryMovement->batch_id,
                $InventoryMovement->movement_type,
                $InventoryMovement->quantity,
                $InventoryMovement->unit_cost,
                $InventoryMovement->notes,
                $InventoryMovement->created_by
            );
        })->all();
    }

    public function find(int $id): ?InvMoveEntity
    {
        $InventoryMovement = InventoryMovement::find($id);

        return $InventoryMovement
            ? new InvMoveEntity(
                $InventoryMovement->id,
                $InventoryMovement->raw_material_id,
                $InventoryMovement->batch_id,
                $InventoryMovement->movement_type,
                $InventoryMovement->quantity,
                $InventoryMovement->unit_cost,
                $InventoryMovement->notes,
                $InventoryMovement->created_by
            )
            : null;
    }

    public function create(InvMoveEntity $entity): InvMoveEntity
    {
        $InventoryMovement = InventoryMovement::create($entity->toArray());

        return new InvMoveEntity(
            $InventoryMovement->id,
            $InventoryMovement->raw_material_id,
            $InventoryMovement->batch_id,
            $InventoryMovement->movement_type,
            $InventoryMovement->quantity,
            $InventoryMovement->unit_cost,
            $InventoryMovement->notes,
            $InventoryMovement->created_by
        );
    }

    public function update(InvMoveEntity $entity): bool
    {
        $InventoryMovement = InventoryMovement::find($entity->id);

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
}
