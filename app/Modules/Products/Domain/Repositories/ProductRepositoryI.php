<?php

namespace App\Modules\Products\Domain\Repositories;

use App\Modules\Products\Domain\Entities\ProductEntity;

interface ProductRepositoryI
{
    public function all(array $filters = []): array;

    public function find(string $id): ?ProductEntity;

    public function create(array $data): ?ProductEntity;

    public function update(array $data): bool;

    public function delete(string $id): bool;
}
