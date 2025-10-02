<?php

namespace App\Modules\Batches\Domain\Services;

use App\Modules\Batches\Domain\Entities\BatcheEntity;
use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

class BatcheService
{
    private BatcheRepositoryI $repository;

    public function __construct(BatcheRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->all($filters, $perPage);
    }

    public function find(int $id): ?BatcheEntity
    {
        return $this->repository->find($id);
    }

    public function create(array $data): bool
    {
        $batche = BatcheEntity::fromArray($data);

        return $this->repository->create($batche);
    }

    public function update(array $data): bool
    {
        $batche = BatcheEntity::fromArray($data);

        return $this->repository->update($batche);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
