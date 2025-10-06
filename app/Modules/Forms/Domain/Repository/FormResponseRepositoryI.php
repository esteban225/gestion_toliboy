<?php

namespace App\Modules\Forms\Domain\Repository;

use App\Modules\Forms\Domain\Entities\FormResponseEntity;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interfaz del repositorio de respuestas de formularios.
 *
 * Define las operaciones básicas que debe implementar cualquier repositorio
 * que maneje las respuestas de formularios en el sistema.
 */
interface FormResponseRepositoryI
{
    /**
     * Obtiene un listado paginado de respuestas de formulario aplicando filtros opcionales.
     *
     * @param  array  $filter  Arreglo asociativo con los filtros a aplicar (campo => valor)
     * @param  int  $perPage  Número de elementos por página
     * @return LengthAwarePaginator Resultados paginados de respuestas de formulario
     */
    public function all($filter = [], $perPage = 15): LengthAwarePaginator;

    /**
     * Busca una respuesta de formulario por su ID y la retorna como entidad.
     *
     * @param  int  $id  Identificador de la respuesta de formulario
     * @return FormResponseEntity|null La entidad encontrada o null si no existe
     */
    public function findId(int $id): ?FormResponseEntity;

    /**
     * Crea una nueva respuesta de formulario en el almacenamiento.
     *
     * @param  FormResponseEntity  $formResponse  Entidad con los datos de la respuesta a crear
     * @return bool True si se creó correctamente, false en caso contrario
     */
    public function create(FormResponseEntity $formResponse): bool;

    /**
     * Actualiza una respuesta de formulario existente en el almacenamiento.
     *
     * @param  FormResponseEntity  $formResponse  Entidad con los datos actualizados
     * @return bool True si se actualizó correctamente, false si no se encontró o hubo error
     */
    public function update(FormResponseEntity $formResponse): bool;

    /**
     * Elimina una respuesta de formulario del almacenamiento.
     *
     * @param  int  $id  Identificador de la respuesta de formulario a eliminar
     * @return bool True si se eliminó correctamente, false si no se encontró
     */
    public function delete(int $id): bool;

    /**
     * Crea una nueva respuesta y sus valores asociados
     *
     * @param FormResponseEntity $entity
     * @param array $values
     * @return mixed
     */
    public function createWithValues(FormResponseEntity $entity, array $values);
}
