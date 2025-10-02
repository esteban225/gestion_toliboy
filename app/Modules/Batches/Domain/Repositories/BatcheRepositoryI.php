<?php

namespace App\Modules\Batches\Domain\Repositories;

use App\Modules\Batches\Domain\Entities\BatcheEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface BatcheRepositoryI
{
    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?BatcheEntity;

    public function create(BatcheEntity $data): bool;

    public function update(BatcheEntity $data): bool;

    public function delete(int $id): bool;
}
