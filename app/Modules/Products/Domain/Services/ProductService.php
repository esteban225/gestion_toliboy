<?php

namespace App\Modules\Products\Domain\Services;

use App\Modules\Products\Domain\Entities\ProductEntity;
use App\Modules\Products\Domain\Repositories\ProductRepositoryI;

class ProductService
{
    private ProductRepositoryI $repository;

    public function __construct(ProductRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = []): array
    {
        return $this->repository->all($filters);
    }

    public function find(string $id): ?ProductEntity
    {
        return $this->repository->find($id);
    }

    public function create(array $data): ?ProductEntity
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
