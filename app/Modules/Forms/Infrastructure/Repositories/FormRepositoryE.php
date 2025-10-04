<?php

namespace App\Modules\Forms\Infrastructure\Repositories;

use App\Models\Form;
use App\Modules\Forms\Domain\Entities\FormEntity;
use App\Modules\Forms\Domain\Repository\FormRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repositorio de formularios que implementa FormRepositoryI.
 *
 * Provee métodos para consultar, crear, actualizar y eliminar formularios.
 */
class FormRepositoryE implements FormRepositoryI
{
    /**
     * Obtiene una lista paginada de formularios aplicando filtros.
     *
     * @param  array  $filters  Array asociativo de filtros donde cada clave es un campo de la tabla y el valor el criterio de búsqueda.
     * @param  int  $perPage  Cantidad de resultados por página.
     * @return LengthAwarePaginator Paginador con los resultados de la consulta.
     */
    public function all(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = Form::query();
        foreach ($filters as $key => $value) {
            if (! empty($value)) {
                $query->where($key, 'like', "%$value%");
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca un formulario por su ID.
     *
     * @param  int  $id  Identificador del formulario.
     * @return FormEntity|null Entidad del formulario si se encuentra, null en caso contrario.
     */
    public function find(int $id): ?FormEntity
    {
        $form = Form::find($id);
        if (! $form) {
            return null;
        }

        return FormEntity::fromModel($form);
    }

    /**
     * Crea un nuevo formulario.
     *
     * @param  FormEntity  $data  Datos del formulario encapsulados en una entidad.
     * @return bool True si la creación fue exitosa, false si ocurrió un error.
     */
    public function create(FormEntity $data): bool
    {
        $form = new Form($data->toArray());

        return $form->save();
    }

    /**
     * Actualiza un formulario existente.
     *
     * @param  FormEntity  $data  Entidad del formulario con datos actualizados.
     * @return bool True si la actualización fue exitosa, false si el formulario no existe o falla.
     */
    public function update(FormEntity $data): bool
    {
        $form = Form::find($data->getId());
        if (! $form) {
            return false;
        }
        $form->fill($data->toArray());

        return $form->save();
    }

    /**
     * Elimina un formulario por su ID.
     *
     * @param  int  $id  Identificador del formulario a eliminar.
     * @return bool True si la eliminación fue exitosa, false si el formulario no existe.
     */
    public function delete(int $id): bool
    {
        $form = Form::find($id);
        if (! $form) {
            return false;
        }

        return $form->delete() > 0;
    }
}
