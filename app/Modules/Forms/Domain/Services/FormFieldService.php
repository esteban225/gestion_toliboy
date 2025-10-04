<?php

namespace App\Modules\Forms\Domain\Services;

use App\Modules\Forms\Domain\Entities\FormFieldEntity;
use App\Modules\Forms\Domain\Repository\FormFieldRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

class FormFieldService
{
    private FormFieldRepositoryI $repository;

    public function __construct(FormFieldRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function list(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->all($filters, $perPage);
    }

    public function find(int $id): ?FormFieldEntity
    {
        return $this->repository->find($id);
    }

    public function create(FormFieldEntity $entity): bool
    {
        return $this->repository->create($entity);
    }

    public function update(FormFieldEntity $entity): bool
    {
        return $this->repository->update($entity);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
