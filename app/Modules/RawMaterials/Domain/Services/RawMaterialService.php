<?php

namespace App\Modules\RawMaterials\Domain\Services;

use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;
use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

class RawMaterialService
{
    private RawMaterialRepositoryI $repository;

    public function __construct(RawMaterialRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->all($filters, $perPage);
    }

    public function find(int $id): ?RawMaterialEntity
    {
        return $this->repository->find($id);
    }

    public function create(RawMaterialEntity $entity): ?RawMaterialEntity
    {
        return $this->repository->create($entity);
    }

    public function update(RawMaterialEntity $entity): bool
    {
        return $this->repository->update($entity);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
