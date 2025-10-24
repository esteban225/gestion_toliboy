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

        return $query->get()->map(fn ($user) => UserEntity::fromArray($user->toArray()))->all();
    }

    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::query();

        // Aplicar filtros si existen
        foreach ($filters as $key => $value) {
            $query->where($key, 'like', '%'.$value.'%');
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return UserEntity|null Entidad de usuario o null si no existe
     */
    public function find(int $id): ?UserEntity
    {
        $user = User::find($id);
        if (! $user) {
            return null;
        }

        return UserEntity::fromArray($user->toArray());
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

        return UserEntity::fromArray($user->toArray());
    }

    /**
     * Actualiza un usuario existente.
     *
     * @param  UserEntity  $data  Datos actualizados del usuario (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function update(UserEntity $data): ?UserEntity
    {
        $user = User::find($data->getId());

        if (!$user) {
            return null; // ❌ antes devolvía false, debe ser null porque el método espera ?UserEntity
        }

        // Filtra los campos nulos
        $updateData = array_filter($data->toArray(), fn($value) => $value !== null);

        // ⚙️ Si no se envía contraseña, se mantiene la existente
        if (empty($updateData['password'])) {
            $updateData['password'] = $user->password;
        } else {
            $updateData['password'] = Hash::make($updateData['password']);
        }

        // Ejecuta la actualización
        $user->update($updateData);

        // ✅ Retorna la entidad actualizada
        return UserEntity::fromArray($user->toArray());
    }



    /**
     * Elimina un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function delete(int $id): bool
    {
        $user = User::find($id);
        if ($user) {
            return (bool) $user->delete() > 0;
        }

        return false;
    }
}
