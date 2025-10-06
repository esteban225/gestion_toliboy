<?php

namespace App\Modules\Forms\Infrastructure\Repositories;

use App\Models\FormResponse;
use App\Modules\Forms\Domain\Entities\FormResponseEntity;
use App\Modules\Forms\Domain\Repository\FormResponseRepositoryI; // Ajusta el namespace de tu modelo Eloquent real
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repositorio de respuestas de formularios basado en Eloquent.
 *
 * Esta clase implementa el repositorio de respuestas de formularios utilizando
 * el ORM Eloquent para acceder a la base de datos.
 */
class FormResponseRepositoryE implements FormResponseRepositoryI
{
    /**
     * Obtiene un listado paginado de respuestas de formulario aplicando filtros opcionales.
     *
     * @param  array  $filter  Arreglo asociativo con los filtros a aplicar (campo => valor)
     * @param  int  $perPage  Número de elementos por página
     * @return LengthAwarePaginator Resultados paginados de respuestas de formulario
     */
    public function all($filter = [], $perPage = 15): LengthAwarePaginator
    {
        $query = FormResponse::query();

        foreach ($filter as $key => $value) {
            if (! is_null($value)) {
                $query->where($key, $value);
            }
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca una respuesta de formulario por su ID y la retorna como entidad.
     *
     * @param  int  $id  Identificador de la respuesta de formulario
     * @return FormResponseEntity|null La entidad encontrada o null si no existe
     */
    public function findId(int $id): ?FormResponseEntity
    {
        $formResponse = FormResponse::find($id);
        if (! $formResponse) {
            return null;
        }

        return FormResponseEntity::fromModel($formResponse);
    }

    /**
     * Crea una nueva respuesta de formulario en la base de datos.
     *
     * @param  FormResponseEntity  $data  Entidad con los datos de la respuesta a crear
     * @return bool True si se creó correctamente, false en caso contrario
     */
    public function create(FormResponseEntity $data): bool
    {
        $formResponse = new FormResponse($data->toArray());

        return $formResponse->save();
    }

    /**
     * Actualiza una respuesta de formulario existente en la base de datos.
     *
     * @param  FormResponseEntity  $data  Entidad con los datos actualizados
     * @return bool True si se actualizó correctamente, false si no se encontró o hubo error
     */
    public function update(FormResponseEntity $data): bool
    {
        $formResponse = FormResponse::find($data->getId());
        if (! $formResponse) {
            return false;
        }
        $formResponse->fill($data->toArray());

        return $formResponse->save();
    }

    /**
     * Elimina una respuesta de formulario de la base de datos.
     *
     * @param  int  $id  Identificador de la respuesta de formulario a eliminar
     * @return bool True si se eliminó correctamente, false si no se encontró
     */
    public function delete(int $id): bool
    {
        $model = FormResponse::find($id);
        if (! $model) {
            return false;
        }

        return $model->delete();
    }

    /**
     * Crea una nueva respuesta y sus valores asociados
     *
     * @return mixed
     */
    public function createWithValues(FormResponseEntity $entity, array $values)
    {
        $formResponse = new FormResponse($entity->toArray());
        $formResponse->save();

        // Guardar los valores de los campos
        foreach ($values as $fieldCode => $value) {
            $field = \App\Models\FormField::where('field_code', $fieldCode)->first();
            if (! $field) {
                continue;
            }
            $formResponse->form_response_values()->create([
                'field_id' => $field->id,
                'value' => is_array($value) ? json_encode($value) : $value,
            ]);
        }

        // Retornar la respuesta con relaciones cargadas
        return $formResponse->load(['user:id,name', 'form:id,name,code', 'form_response_values.form_field']);
        // return FormResponseEntity::fromModel($formResponse);
    }
}
