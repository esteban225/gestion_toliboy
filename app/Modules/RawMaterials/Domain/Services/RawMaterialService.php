<?php

namespace App\Modules\RawMaterials\Domain\Services;

use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;
use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;


class RawMaterialService
{
    private RawMaterialRepositoryI $repository;

    public function __construct(RawMaterialRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = []): array
    {
        return $this->repository->all($filters);
    }

    public function find(string $id): ?RawMaterialEntity
    {
        return $this->repository->find($id);
    }

    public function create(array $data): ?RawMaterialEntity
    {
        return $this->repository->create($data);
    }

    public function update(array $data): bool
    {
        return $this->repository->update($data);
    }

    public function delete(string $id): bool
    {
        return $this->repository->delete($id);
    }
}