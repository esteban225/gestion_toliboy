<?php

namespace App\Modules\Users\Infrastructure\Repositories;

use App\Models\User;
use App\Modules\Users\Domain\Entities\UserEntity;
use App\Modules\Users\Domain\Repositories\UsersRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

/**
 * Repositorio de usuarios basado en Eloquent ORM.
 *
 * Implementa UsersRepositoryInterface para desacoplar la lógica de persistencia del dominio.
 * Permite listar, buscar, crear, actualizar y eliminar usuarios usando el modelo Eloquent User.
 *
 * Principios SOLID aplicados:
 * - SRP: Solo gestiona la persistencia de usuarios.
 * - OCP: Puede extenderse con otros repositorios sin modificar esta clase.
 * - LSP: Puede ser sustituido por otra implementación de la interfaz sin romper el sistema.
 * - ISP: Implementa solo los métodos definidos en la interfaz específica.
 * - DIP: El dominio depende de la abstracción UsersRepositoryInterface, no de esta implementación concreta.
 */
class EloquentUsersRepository implements UsersRepositoryInterface
{
    /**
     * Obtiene todos los usuarios, opcionalmente filtrados.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['role_id' => 2])
     * @return UserEntity[]|array Lista de entidades de usuario
     */
    public function all(array $filters = []): array
    {
        $query = User::query();

        // Aplicar filtros si existen
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get()->map(fn ($user) => $this->mapToEntity($user))->all();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        // Aplicar filtros si existen
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        $paginator = $query->paginate($perPage);

        $paginator->getCollection()->transform(fn ($user) => $this->mapToEntity($user));

        return $paginator;
    }

    /**
     * Busca un usuario por su identificador.
     *
     * @param  string  $id  Identificador único del usuario
     * @return UserEntity|null Entidad de usuario o null si no existe
     */
    public function find(string $id): ?UserEntity
    {
        $user = User::find($id);
        if (! $user) {
            return null;
        }

        return $this->mapToEntity($user);
    }

    /**
     * Crea un nuevo usuario.
     *
     * @param  UserEntity  $data  Datos del usuario (name, email, password, role_id, etc.)
     * @return UserEntity|null Entidad creada o null si falla
     */
    public function create(UserEntity $data): ?UserEntity
    {
        $data->setPassword(Hash::make($data->getPassword()));
        $user = User::create($data->toArray());

        return $this->mapToEntity($user);
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param  UserEntity  $data  Datos actualizados del usuario (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function update(UserEntity $data): bool
    {
        $user = User::find($data->getId());
        if ($user) {
            return $user->update($data->toArray());
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
        $user = User::find($id);
        if ($user) {
            return (bool) $user->delete() > 0;
        }

        return false;
    }

    private function mapToEntity(User $user): UserEntity
    {
        return new UserEntity(
            $user->id,
            $user->name,
            $user->email,
            $user->password,
            $user->role_id,
            $user->position,
            $user->is_active,
            $user->last_login,
            $user->created_at,
            $user->updated_at
        );
    }
}
