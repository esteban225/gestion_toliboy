<?php

namespace App\Modules\Batches\Application\UseCases;

use App\Modules\Batches\Domain\Services\BatcheService;
use Illuminate\Pagination\LengthAwarePaginator;

class BatcheUseCase
{
    private BatcheService $service;

    public function __construct(BatcheService $service)
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

    public function create(array $data)
    {
        return $this->service->create($data);
    }

    public function update(array $data): bool
    {
        return $this->service->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->service->delete($id);
    }
}
