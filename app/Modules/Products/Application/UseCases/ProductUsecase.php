<?php

namespace App\Modules\Products\Application\UseCases;

use App\Modules\Products\Domain\Services\ProductService;

class ProductUseCase
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    public function list(array $filters = []): array
    {
        return $this->service->list($filters);
    }

    public function find(string $id)
    {
        return $this->service->find($id);
    }

    public function create(array $data)
    {
        return $this->service->create($data);
    }

    public function update(array $data): bool
    {
        return $this->service->update($data);
    }

    public function delete(string $id): bool
    {
        return $this->service->delete($id);
    }
}
