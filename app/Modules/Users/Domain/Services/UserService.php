<?php

namespace App\Modules\Users\Domain\Services;

use App\Modules\Users\Domain\Entities\UserEntity;
use App\Modules\Users\Domain\Repositories\UsersRepositoryInterface;

/**
 * Servicio de dominio para gestión de usuarios.
 *
 * Encapsula la lógica de negocio relacionada con usuarios y delega la persistencia al repositorio.
 *
 * @package App\Modules\Users\Domain\Services
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
     * @param array $filters Filtros de búsqueda (ej: ['role_id' => 2])
     * @return UserEntity[]|array Lista de entidades de usuario
     */
    public function listUsers(array $filters = [])
    {
        $data = $this->users_repository->all($filters);
        return $data;
    }

    /**
     * Obtener un usuario por su identificador.
     *
     * @param string $id Identificador único del usuario
     * @return UserEntity|null Entidad de usuario o null si no existe
     */
    public function getUser(string $id)
    {
        $data = $this->users_repository->find($id);
        return $data;
    }

    /**
     * Crear un nuevo usuario.
     *
     * @param array $data Datos del usuario (name, email, password, role_id, etc.)
     * @return UserEntity|null Entidad creada o null si falla
     */
    public function createUser(array $data): ?UserEntity
    {
        return $this->users_repository->create($data);
    }

    /**
     * Actualizar un usuario existente.
     *
     * @param array $data Datos actualizados del usuario (debe incluir el id)
     * @return bool True si la actualización fue exitosa, false en caso contrario
     */
    public function updateUser(array $data): bool
    {
        return $this->users_repository->update($data);
    }

    /**
     * Eliminar un usuario por su identificador.
     *
     * @param string $id Identificador único del usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function deleteUser(string $id): bool
    {
        return $this->users_repository->delete($id);
    }
}