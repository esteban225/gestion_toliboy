<?php

namespace App\Modules\Forms\Domain\Repository;

use App\Modules\Forms\Domain\Entities\FormFieldEntity;
use Illuminate\Pagination\LengthAwarePaginator;

interface FormFieldRepositoryI
{
    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function find(int $id): ?FormFieldEntity;

    public function create(FormFieldEntity $entity): bool;

    public function update(FormFieldEntity $entity): bool;

    public function delete(int $id): bool;
}
