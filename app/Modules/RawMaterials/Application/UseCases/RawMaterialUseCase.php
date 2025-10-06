<?php

namespace App\Modules\RawMaterials\Application\UseCases;

use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;
use App\Modules\RawMaterials\Domain\Services\RawMaterialService;
use Illuminate\Pagination\LengthAwarePaginator;

class RawMaterialUseCase
{
    private RawMaterialService $service;

    public function __construct(RawMaterialService $service)
    {
        $this->service = $service;
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->service->list($filters, $perPage);
    }

    public function find(int $id): ?RawMaterialEntity
    {
        return $this->service->find($id);
    }

    public function create(RawMaterialEntity $entity)
    {
        return $this->service->create($entity);
    }

    public function update(RawMaterialEntity $entity): bool
    {
        return $this->service->update($entity);
    }

    public function delete(int $id): bool
    {
        return $this->service->delete($id);
    }
}
