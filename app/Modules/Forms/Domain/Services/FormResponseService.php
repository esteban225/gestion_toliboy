<?php

namespace App\Modules\Forms\Domain\Services;

use App\Modules\Forms\Domain\Entities\FormResponseEntity;
use App\Modules\Forms\Domain\Repository\FormResponseRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

class FormResponseService
{
    private FormResponseRepositoryI $repository;

    public function __construct(FormResponseRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    public function all($filter = [], $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->all($filter, $perPage);
    }

    public function findId(int $id): ?FormResponseEntity
    {
        return $this->repository->findId($id);
    }

    public function create(FormResponseEntity $formResponse): bool
    {
        return $this->repository->create($formResponse);
    }

    public function update(FormResponseEntity $formResponse): bool
    {
        return $this->repository->update($formResponse);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
