<?php

namespace App\Modules\RawMaterials\Domain\Repositories;

use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;

interface RawMaterialRepositoryI
{
    public function all(array $filters = []): array;

    public function find(string $id): ?RawMaterialEntity;

    public function create(array $data): ?RawMaterialEntity;

    public function update(array $data): bool;

    public function delete(string $id): bool;

    public function getMaterialsReport(array $filters = []): array;

    public function getLowStockMaterials(): array;
}
