<?php

namespace App\Modules\Users\Domain\Services;

use App\Modules\Users\Domain\Entities\UserEntity;
use App\Modules\Users\Domain\Repositories\UsersRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

/**
 * Servicio de dominio para gestión de usuarios.
 *
 * Encapsula la lógica de negocio relacionada con usuarios y delega la persistencia al repositorio.
 */
class UserService
{
    /**
     * @var UsersRepositoryInterface Repositorio de usuarios
     */
    public function __construct(private UsersRepositoryInterface $users_repository) {}

    /**
     * Listar usuarios con filtros opcionales.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['role_id' => 2])
     * @return UserEntity[]|array Lista de entidades de usuario
     */
    public function listUsers(array $filters = [])
    {
        $data = $this->users_repository->all($filters);

        return $data;
    }

    public function paginateUsers(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->users_repository->paginate($filters, $perPage);
    }

    /**
     * Obtener un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return UserEntity|null Entidad de usuario o null si no existe
     */
    public function getUser(int $id): ?UserEntity
    {
        return $this->users_repository->find($id);
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param  UserEntity  $data  Datos del usuario (name, email, password, role_id, etc.)
     * @return UserEntity|null Entidad creada o null si falla
     */
    public function createUser(UserEntity $data): ?UserEntity
    {
        $data->setPassword(Hash::make(($data->getPassword())));
        $userEntity = $this->users_repository->create($data);

        return $userEntity;
    }

    /**
     * Actualizar un usuario existente.
     *
     * @param  UserEntity  $data  Datos actualizados del usuario (debe incluir el id)
     * @return UserEntity |null Entidad actualizada o null si falla
     */
    public function updateUser(UserEntity $data): ?UserEntity
    {
        if ($data->getPassword()) {
            $data->setPassword(Hash::make($data->getPassword()));
        }

        return $this->users_repository->update($data);
    }

    /**
     * Eliminar un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function deleteUser(int $id): bool
    {
        return $this->users_repository->delete($id);
    }
}
