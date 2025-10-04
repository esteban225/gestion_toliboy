<?php

namespace App\Modules\Forms\Domain\Services;

use App\Modules\Forms\Domain\Entities\FormEntity;
use App\Modules\Forms\Domain\Repository\FormRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Servicio de formularios responsable de coordinar las operaciones de negocio relacionadas con formularios.
 *
 * Utiliza el repositorio para listar, buscar, crear, actualizar y eliminar formularios.
 */
class FormService
{
    private FormRepositoryI $repository;

    /**
     * Constructor del servicio de formularios.
     *
     * @param  FormRepositoryI  $repository  Repositorio que implementa la persistencia de formularios.
     */
    public function __construct(FormRepositoryI $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Obtiene un paginador de formularios aplicando los filtros especificados.
     *
     * @param  array  $filters  Filtros asociativos donde la clave es el campo y el valor el criterio de búsqueda.
     * @param  int  $perpage  Número de resultados por página.
     * @return LengthAwarePaginator|null Paginador con los formularios, o null si no hay resultados.
     */
    public function all(array $filters, int $perpage = 15): ?LengthAwarePaginator
    {
        return $this->repository->all($filters, $perpage);
    }

    /**
     * Busca y retorna un formulario por su identificador.
     *
     * @param  int  $id  Identificador del formulario.
     * @return FormEntity|null Entidad del formulario si existe, null en caso contrario.
     */
    public function find(int $id): ?FormEntity
    {
        return $this->repository->find($id);
    }

    /**
     * Crea un nuevo formulario en base a los datos proporcionados.
     *
     * @param  FormEntity  $data  Entidad que contiene los datos del formulario.
     * @return bool True si la creación fue exitosa, false en caso de error.
     */
    public function create(FormEntity $data): bool
    {
        return $this->repository->create($data);
    }

    /**
     * Actualiza un formulario existente con los datos proporcionados.
     *
     * @param  FormEntity  $data  Entidad que contiene los datos actualizados del formulario.
     * @return bool True si la actualización fue exitosa, false en caso de error o si no existe.
     */
    public function update(FormEntity $data): bool
    {
        return $this->repository->update($data);
    }

    /**
     * Elimina un formulario por su identificador.
     *
     * @param  int  $id  Identificador del formulario a eliminar.
     * @return bool True si la eliminación fue exitosa, false si no existe.
     */
    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }
}
