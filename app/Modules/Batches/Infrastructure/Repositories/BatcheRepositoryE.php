<?php

namespace App\Modules\Batches\Infrastructure\Repositories;

use App\Models\Batch;
use App\Modules\Batches\Domain\Entities\BatcheEntity;
use App\Modules\Batches\Domain\Repositories\BatcheRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repositorio para la gestión de lotes (Batch) en la infraestructura.
 *
 * Implementa la interfaz BatcheRepositoryI para proporcionar métodos de acceso y manipulación
 * de entidades de lote utilizando Eloquent ORM.
 *
 * Métodos:
 * - all(array $filters = [], int $perpage = 15): Obtiene una lista paginada de lotes, permitiendo aplicar filtros.
 * - find(int $id): Busca un lote por su ID y lo retorna como entidad de dominio.
 * - create(BatcheEntity $data): Crea un nuevo lote a partir de una entidad y retorna la entidad creada.
 * - update(BatcheEntity $data): Actualiza un lote existente con los datos proporcionados en la entidad.
 * - delete(int $id): Elimina un lote por su ID.
 */
class BatcheRepositoryE implements BatcheRepositoryI
{
    public function all(array $filters = [], int $perpage = 15): LengthAwarePaginator
    {
        $query = Batch::query();
        foreach ($filters as $field => $value) {
            if (! empty($value)) {
                $query->where($field, $value);
            }
        }

        return $query->paginate($perpage);
    }

    public function find(int $id): ?BatcheEntity
    {
        $batch = Batch::find($id);
        if (! $batch) {
            return null;
        }
        $model = BatcheEntity::fromModel($batch);

        return $model ?: null;
    }

    public function create(BatcheEntity $data): bool
    {
        $dataArray = $data->toArray();
        $batch = Batch::create($dataArray);
        $model = BatcheEntity::fromModel($batch);

        return (bool) $model;
    }

    public function update(BatcheEntity $data): bool
    {
        $batch = Batch::find($data->getId());
        if ($batch) {
            return $batch->update($data->toArray());
        }

        return false;
    }

    public function delete(int $id): bool
    {
        $batch = Batch::find($id);
        if (! $batch) {
            return false;
        }

        return $batch->delete();
    }
}
