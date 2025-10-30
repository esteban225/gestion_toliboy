<?php

namespace App\Modules\DataUser\Infrastructure\Repositories;

use App\Models\PersonalDatum;
use App\Modules\DataUser\Domain\Entities\DataUserEntity;
use App\Modules\DataUser\Domain\Repositories\DataUserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $query = PersonalDatum::query();

        // Aplicar filtros si existen
        foreach ($filters as $key => $value) {
            $query->where($key, 'LIKE', "%$value%");
        }

        $dataUsers = $query->get();

        // Mapear los resultados a entidades
        return $dataUsers->map(fn ($item) => $this->mapToEntity($item))->all();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = PersonalDatum::query();
        foreach ($filters as $key => $value) {
            $query->where($key , 'LIKE', "%$value%");
        }
        $paginator = $query->paginate($perPage);
        return $paginator;
    }

    /**
     * Busca datos de usuario por su identificador.
     *
     * @param  string  $id  Identificador único de los datos de usuario
     * @return DataUserEntity|null Entidad de datos de usuario o null si no existe
     */
    public function find(string $id): ?DataUserEntity
    {
        $dataUser = PersonalDatum::find($id);
        if (! $dataUser) {
            return null;
        }

        return $this->mapToEntity($dataUser);
    }

    /**
     * Crea nuevos datos de usuario.
     *
     * @param  array  $data  Datos del usuario (campos específicos del módulo DataUser)
     * @return DataUserEntity|null Entidad creada o null si falla
     */
    public function create(array $data): ?DataUserEntity
    {
        $dataUser = PersonalDatum::create($data);
        if (! $dataUser) {
            return null;
        }

        return $this->mapToEntity($dataUser);
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

        $dataUser = PersonalDatum::find($data['id']);
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
        $dataUser = PersonalDatum::find($id);
        if ($dataUser) {
            return (bool) $dataUser->delete() > 0;
        }

        return false;
    }

    protected function mapToEntity(PersonalDatum $model): DataUserEntity
    {
        return new DataUserEntity(
            $model->id,
            $model->user_id,
            $model->num_phone,
            $model->num_phone_alt,
            $model->num_identification,
            $model->identification_type,
            $model->address,
            $model->emergency_contact,
            $model->emergency_phone,
            $model->created_at,
            $model->updated_at
        );
    }
}
