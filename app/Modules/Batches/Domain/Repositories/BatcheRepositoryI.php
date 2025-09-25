<?php

namespace App\Modules\Batches\Domain\Repositories;

use App\Modules\Batches\Domain\Entities\BatcheEntity;

interface BatcheRepositoryI
{
    public function all(array $filters = []): array;

    public function find(string $id): ?BatcheEntity;

    public function create(array $data): ?BatcheEntity;

    public function update(array $data): bool;

    public function delete(string $id): bool;
}
