<?php

namespace App\Modules\Forms\Application\UseCases;

use App\Modules\Forms\Domain\Entities\FormFieldEntity;
use App\Modules\Forms\Domain\Services\FormFieldService;
use Illuminate\Pagination\LengthAwarePaginator;

class ManageFormFieldUseCase
{
    private FormFieldService $service;

    public function __construct(FormFieldService $service)
    {
        $this->service = $service;
    }

    public function listFormFields(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->service->list($filters, $perPage);
    }

    public function findFormField(int $id): ?FormFieldEntity
    {
        return $this->service->find($id);
    }

    public function createFormField(array $data): bool
    {
        $entity = FormFieldEntity::fromArray($data);

        return $this->service->create($entity);
    }

    public function updateFormField(array $data): bool
    {
        $entity = FormFieldEntity::fromArray($data);

        return $this->service->update($entity);
    }

    public function deleteFormField(int $id): bool
    {
        return $this->service->delete($id);
    }
}
