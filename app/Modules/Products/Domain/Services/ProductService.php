<?php

namespace App\Modules\Products\Domain\Services;

use App\Modules\Products\Domain\Entities\ProductEntity;
use App\Modules\Products\Domain\Repositories\ProductRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    private ProductRepositoryI $repository;

    public function __construct(ProductRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->all($filters, $perPage);
    }

    public function find(int $id): ?ProductEntity
    {
        return $this->repository->find($id);
    }

    public function create(ProductEntity $data): ?ProductEntity
    {
        return $this->repository->create($data);
    }

    public function update(ProductEntity $data): bool
    {
        return $this->repository->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
