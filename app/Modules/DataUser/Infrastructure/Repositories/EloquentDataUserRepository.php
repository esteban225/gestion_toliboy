<?php

namespace App\Modules\DataUser\Infrastructure\Repositories;

use App\Models\PersonalDatum as DataUserModel;
use App\Modules\DataUser\Domain\Entities\DataUserEntity;
use App\Modules\DataUser\Domain\Repositories\DataUserRepositoryInterface;

/**
 * Implementación del repositorio de datos de usuario utilizando Eloquent ORM.
 *
 * Esta clase proporciona métodos para interactuar con la base de datos
 * y realizar operaciones CRUD sobre los datos adicionales de usuario.
 *
 * Principios SOLID aplicados:
 * - SRP: Cada método tiene una única responsabilidad relacionada con la persistencia.
 * - OCP: Puede extenderse con nuevas funcionalidades sin modificar el código existente.
 * - LSP: Cumple con el contrato definido en DataUserRepositoryInterface.
 * - ISP: Implementa solo los métodos necesarios para la gestión de datos de usuario.
 * - DIP: Depende de abstracciones (DataUserRepositoryInterface) en lugar de concreciones.
 */
class EloquentDataUserRepository implements DataUserRepositoryInterface
{
    /**
     * Obtiene todos los datos de usuarios, opcionalmente filtrados.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['status' => 'active'])
     * @return DataUserEntity[]|array Lista de entidades de datos de usuario
     */
    public function all(array $filters = []): array
    {
        $query = DataUserModel::query();

        // Aplicar filtros si existen
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get()->map(function ($dataUser) {
            return new DataUserEntity(
                // Mapear los campos del modelo a la entidad
                $dataUser->id,
                $dataUser->user_id,
                $dataUser->num_phone,
                $dataUser->num_phone_alt,
                $dataUser->num_identification,
                $dataUser->identification_type,
                $dataUser->address,
                $dataUser->emergency_contact,
                $dataUser->emergency_phone,
                $dataUser->created_at,
                $dataUser->updated_at
            );
        })->all();
    }

    /**
     * Busca datos de usuario por su identificador.
     *
     * @param  string  $id  Identificador único de los datos de usuario
     * @return DataUserEntity|null Entidad de datos de usuario o null si no existe
     */
    public function find(string $id): ?DataUserEntity
    {
        $dataUser = DataUserModel::find($id);
        if (! $dataUser) {
            return null;
        }

        return new DataUserEntity(
            // Mapear los campos del modelo a la entidad
            $dataUser->id,
            $dataUser->user_id,
            $dataUser->num_phone,
            $dataUser->num_phone_alt,
            $dataUser->num_identification,
            $dataUser->identification_type,
            $dataUser->address,
            $dataUser->emergency_contact,
            $dataUser->emergency_phone,
            $dataUser->created_at,
            $dataUser->updated_at
        );
    }

    /**
     * Crea nuevos datos de usuario.
     *
     * @param  array  $data  Datos del usuario (campos específicos del módulo DataUser)
     * @return DataUserEntity|null Entidad creada o null si falla
     */
    public function create(array $data): ?DataUserEntity
    {
        $dataUser = DataUserModel::create($data);
        if (! $dataUser) {
            return null;
        }

        return new DataUserEntity(
            // Mapear los campos del modelo a la entidad
            $dataUser->id,
            $dataUser->user_id,
            $dataUser->num_phone,
            $dataUser->num_phone_alt,
            $dataUser->num_identification,
            $dataUser->identification_type,
            $dataUser->address,
            $dataUser->emergency_contact,
            $dataUser->emergency_phone,
            $dataUser->created_at,
            $dataUser->updated_at

        );
    }

    /**
     * Actualiza datos de usuario existentes.
     *
     * @param  array  $data  Datos actualizados (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function update(array $data): bool
    {
        if (! isset($data['id'])) {
            return false; // No se puede actualizar sin ID
        }

        $dataUser = DataUserModel::find($data['id']);
        if (! $dataUser) {
            return false; // No se encontró el registro
        }

        return $dataUser->update($data);
    }

    /**
     * Elimina datos de usuario por su identificador.
     *
     * @param  string  $id  Identificador único de los datos de usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function delete(string $id): bool
    {
        $dataUser = DataUserModel::find($id);
        if (! $dataUser) {
            return false; // No se encontró el registro
        }

        return $dataUser->delete();
    }
}
