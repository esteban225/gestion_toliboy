<?php

namespace App\Modules\Forms\Application\UseCases;

use App\Modules\Forms\Domain\Entities\FormResponseEntity;
use App\Modules\Forms\Domain\Services\FormResponseService;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Caso de uso para gestionar respuestas de formularios.
 *
 * Encapsula la lógica de aplicación para listar, obtener, crear, actualizar y eliminar respuestas de formularios.
 */
class ManageFormResponseUseCase
{
    public function __construct(private FormResponseService $formResponseService)
    {
        $this->formResponseService = $formResponseService;
    }

    public function all($filter = [], $perPage = 15): LengthAwarePaginator
    {
        return $this->formResponseService->all($filter, $perPage);
    }

    public function findId(int $id): ?FormResponseEntity
    {
        return $this->formResponseService->findId($id);
    }

    public function create(array $formResponse): bool
    {
        $formResponse = FormResponseEntity::fromArray($formResponse);

        return $this->formResponseService->create($formResponse);
    }

    public function update(array $formResponse): bool
    {
        $formResponse = FormResponseEntity::fromArray($formResponse);

        return $this->formResponseService->update($formResponse);
    }

    public function delete(int $id): bool
    {
        return $this->formResponseService->delete($id);
    }
}
