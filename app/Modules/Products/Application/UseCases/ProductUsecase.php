<?php

namespace App\Modules\Products\Application\UseCases;

use App\Modules\Products\Domain\Entities\ProductEntity;
use App\Modules\Products\Domain\Services\ProductService;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductUseCase
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->service->list($filters, $perPage);
    }

    public function find(string $id)
    {
        return $this->service->find($id);
    }

    public function create(ProductEntity $entity): ?ProductEntity
    {
        return $this->service->create($entity);
    }

    public function update(ProductEntity $entity, int $id): bool
    {
        $entity->setId($id);

        return $this->service->update($entity);
    }

    public function delete(string $id): bool
    {
        return $this->service->delete($id);
    }
}
