<?php

namespace App\Modules\RawMaterials\Infrastructure\Repositories;

use App\Models\RawMaterial;
use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;
use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

class RawMaterialRepositoryE implements RawMaterialRepositoryI
{
    /**
     * Obtiene todos los usuarios, opcionalmente filtrados.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['role_id' => 2])
     * @return RawMaterialEntity[]|array Lista de entidades de usuario
     */
    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {

        $query = RawMaterial::query();
        // Aplicar filtros si existen
        foreach ($filters as $key => $value) {
            $query->where($key, 'LIKE', "%$value%");
        }

        return $query->paginate($perPage);

    }

    /**
     * Busca un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return RawMaterialEntity|null Entidad de usuario o null si no existe
     */
    public function find(int $id): ?RawMaterialEntity
    {
        $RawMaterial = RawMaterial::find($id);

        return RawMaterialEntity::fromModel($RawMaterial);
    }

    /**
     * Crea un nuevo usuario.
     *
     * @param  RawMaterialEntity  $data  Datos del usuario (name, email, password, role_id, etc.)
     * @return RawMaterialEntity|null Entidad creada o null si falla
     */
    public function create(RawMaterialEntity $data): ?RawMaterialEntity
    {
        $RawMaterial = RawMaterial::create($data->toArray());

        return RawMaterialEntity::fromModel($RawMaterial);
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param  RawMaterialEntity  $data  Datos actualizados del usuario (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function update(RawMaterialEntity $data): bool
    {
        $RawMaterial = RawMaterial::find($data->getId());
        if ($RawMaterial) {
            return $RawMaterial->update($data->toArray());
        }

        return false;
    }

    /**
     * Elimina un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function delete(int $id): bool
    {
        $RawMaterial = RawMaterial::find($id);
        if ($RawMaterial) {
            return (bool) $RawMaterial->delete() > 0;
        }

        return false;
    }
}
