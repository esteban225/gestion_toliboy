<?php

namespace App\Modules\Forms\Application\UseCases;

use App\Modules\Forms\Domain\Entities\FormEntity;
use App\Modules\Forms\Domain\Services\FormService;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Caso de uso para gestionar formularios.
 *
 * Encapsula la lógica de aplicación para listar, obtener, crear, actualizar y eliminar formularios.
 */
class ManageFormUseCase
{
    public function __construct(private FormService $formService) {}

    /**
     * Lista formularios paginados aplicando los filtros indicados.
     *
     * @param  array  $filters  Filtros asociativos donde la clave es el campo y el valor el criterio de búsqueda.
     * @param  int  $perpage  Número de resultados por página.
     * @return LengthAwarePaginator Paginador con los formularios.
     */
    public function list(array $filters, int $perpage = 15): LengthAwarePaginator
    {
        return $this->formService->all($filters, $perpage);
    }

    /**
     * Obtiene un formulario por su identificador.
     *
     * @param  int  $id  Identificador del formulario.
     * @return FormEntity|null Entidad del formulario si existe, null en caso contrario.
     */
    public function get(int $id): ?FormEntity
    {
        return $this->formService->find($id);
    }

    /**
     * Crea un nuevo formulario a partir de los datos proporcionados.
     *
     * @param  array  $data  Datos del formulario.
     * @return bool True si la creación fue exitosa, false en caso de error.
     */
    public function create(array $data): bool
    {
        $entity = FormEntity::fromArray($data);

        return $this->formService->create($entity);
    }

    /**
     * Actualiza un formulario existente con los datos proporcionados.
     *
     * @param  array  $data  Datos actualizados del formulario.
     * @return bool True si la actualización fue exitosa, false en caso de error.
     */
    public function update(array $data): bool
    {
        $entity = FormEntity::fromArray($data);

        return $this->formService->update($entity);
    }

    /**
     * Elimina un formulario por su identificador.
     *
     * @param  int  $id  Identificador del formulario a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso de error.
     */
    public function delete(int $id): bool
    {
        return $this->formService->delete($id);
    }
}
