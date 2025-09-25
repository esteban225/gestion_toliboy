<?php

namespace App\Modules\Batches\Domain\Services;

use App\Modules\Batches\Domain\Entities\BatcheEntity;
use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;

class BatcheService
{
    private BatcheRepositoryI $repository;

    public function __construct(BatcheRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = []): array
    {
        return $this->repository->all($filters);
    }

    public function find(string $id): ?BatcheEntity
    {
        return $this->repository->find($id);
    }

    public function create(array $data): ?BatcheEntity
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
