<?php

namespace App\Modules\Forms\Domain\Repository;

use App\Modules\Forms\Domain\Entities\FormEntity;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interfaz del repositorio de formularios.
 *
 * Define los métodos para listar, buscar, crear, actualizar y eliminar formularios.
 */
interface FormRepositoryI
{
    /**
     * Obtiene un listado paginado de formularios aplicando filtros.
     *
     * @param  array  $filters  Filtros asociativos donde la clave es el campo y el valor el criterio de búsqueda.
     * @param  int  $perpage  Número de resultados por página.
     * @return LengthAwarePaginator Paginador con los formularios.
     */
    public function all(array $filters, int $perpage = 15): LengthAwarePaginator;

    /**
     * Busca un formulario por su identificador.
     *
     * @param  int  $id  Identificador del formulario.
     * @return FormEntity|null Entidad del formulario si existe, null en caso contrario.
     */
    public function find(int $id): ?FormEntity;

    /**
     * Crea un nuevo formulario.
     *
     * @param  FormEntity  $data  Datos del formulario encapsulados en una entidad.
     * @return bool True si la creación fue exitosa, false en caso de error.
     */
    public function create(FormEntity $data): bool;

    /**
     * Actualiza un formulario existente.
     *
     * @param  FormEntity  $data  Entidad del formulario con los datos a actualizar.
     * @return bool True si la actualización fue exitosa, false en caso de que no exista o falle.
     */
    public function update(FormEntity $data): bool;

    /**
     * Elimina un formulario por su identificador.
     *
     * @param  int  $id  Identificador del formulario a eliminar.
     * @return bool True si la eliminación fue exitosa, false si no existe.
     */
    public function delete(int $id): bool;
}
