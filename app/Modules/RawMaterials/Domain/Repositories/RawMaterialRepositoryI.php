<?php

namespace App\Modules\RawMaterials\Domain\Repositories;

use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface RawMaterialRepositoryI
{
    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?RawMaterialEntity;

    public function create(RawMaterialEntity $entity): ?RawMaterialEntity;

    public function update(RawMaterialEntity $entity): bool;

    public function delete(int $id): bool;
}
