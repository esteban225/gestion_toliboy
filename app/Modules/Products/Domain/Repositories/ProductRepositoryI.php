<?php

namespace App\Modules\Products\Domain\Repositories;

use App\Modules\Products\Domain\Entities\ProductEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepositoryI
{
    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?ProductEntity;

    public function create(ProductEntity $entity): ?ProductEntity;

    public function update(ProductEntity $entity): bool;

    public function delete(int $id): bool;
}
