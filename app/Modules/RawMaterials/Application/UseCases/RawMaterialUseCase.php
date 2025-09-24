<?php

namespace App\Modules\RawMaterials\Application\UseCases;

use App\Modules\RawMaterials\Domain\Services\RawMaterialService;

class RawMaterialUseCase
{
    private RawMaterialService $service;

    public function __construct(RawMaterialService $service)
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
