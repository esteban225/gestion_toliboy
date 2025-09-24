<?php

namespace App\Modules\RawMaterials\Infrastructure\Repositories;

use App\Models\RawMaterial;
use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;
use App\Modules\RawMaterials\Domain\Repositories\RawMaterialRepositoryI;

class RawMaterialRepositoryE implements RawMaterialRepositoryI
{
    /**
     * Obtiene todos los usuarios, opcionalmente filtrados.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['role_id' => 2])
     * @return RawMaterialEntity[]|array Lista de entidades de usuario
     */
    public function all(array $filters = []): array
    {

        $query = RawMaterial::query();

        // Aplicar filtros si existen
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get()->map(function ($RawMaterial) {
            return new RawMaterialEntity(
                $RawMaterial->id,
                $RawMaterial->name,
                $RawMaterial->code,
                $RawMaterial->description,
                $RawMaterial->unit_of_measure,
                $RawMaterial->stock,
                $RawMaterial->min_stock,
                $RawMaterial->is_active,
                $RawMaterial->created_by
            );
        })->all();
    }

    /**
     * Busca un usuario por su identificador.
     *
     * @param  string  $id  Identificador único del usuario
     * @return RawMaterialEntity|null Entidad de usuario o null si no existe
     */
    public function find(string $id): ?RawMaterialEntity
    {
        $RawMaterial = RawMaterial::find($id);

        return $RawMaterial
            ? new RawMaterialEntity(
                $RawMaterial->id,
                $RawMaterial->name,
                $RawMaterial->code,
                $RawMaterial->description,
                $RawMaterial->unit_of_measure,
                $RawMaterial->stock,
                $RawMaterial->min_stock,
                $RawMaterial->is_active,
                $RawMaterial->created_by
            )
            : null;
    }

    /**
     * Crea un nuevo usuario.
     *
     * @param  array  $data  Datos del usuario (name, email, password, role_id, etc.)
     * @return RawMaterialEntity|null Entidad creada o null si falla
     */
    public function create(array $data): ?RawMaterialEntity
    {
        $RawMaterial = RawMaterial::create($data);

        return new RawMaterialEntity(
            $RawMaterial->id,
            $RawMaterial->name,
            $RawMaterial->code,
            $RawMaterial->description,
            $RawMaterial->unit_of_measure,
            $RawMaterial->stock,
            $RawMaterial->min_stock,
            $RawMaterial->is_active,
            $RawMaterial->created_by
        );
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param  array  $data  Datos actualizados del usuario (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function update(array $data): bool
    {
        $RawMaterial = RawMaterial::find($data['id']);
        if ($RawMaterial) {
            return $RawMaterial->update($data);
        }

        return false;
    }

    /**
     * Elimina un usuario por su identificador.
     *
     * @param  string  $id  Identificador único del usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function delete(string $id): bool
    {
        $RawMaterial = RawMaterial::find($id);
        if ($RawMaterial) {
            return (bool) $RawMaterial->delete() > 0;
        }

        return false;
    }

    public function getMaterialsReport(array $filters = []): array
    {
        // Aquí la lógica para generar el reporte de materiales
        // Ejemplo: retornar un array vacío para cumplir la interfaz
        return [];
    }

    public function getLowStockMaterials(): array
    {
        // Aquí la lógica para obtener materiales con bajo stock
        return [];
    }
}
