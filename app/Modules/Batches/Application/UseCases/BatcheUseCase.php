<?php

namespace App\Modules\Batches\Application\UseCases;

use App\Modules\Batches\Domain\Services\BatcheService;

class BatcheUseCase
{
    private BatcheService $service;

    public function __construct(BatcheService $service)
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
