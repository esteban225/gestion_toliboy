<?php

namespace App\Modules\Roles\Domain\Services;

use App\Modules\Roles\Domain\Entities\RoleEntity;
use App\Modules\Roles\Domain\Repositories\RoleRepositoryInterface;

/**
 * Servicio para la gestión de roles.
 *
 * Proporciona métodos para obtener, crear, actualizar y eliminar roles
 * utilizando el repositorio de roles.
 *
 * Métodos:
 * - getRoleById(int $id): ?RoleEntity
 *   Obtiene un rol por su identificador.
 *
 * - getAllRoles(): array
 *   Obtiene todos los roles disponibles.
 *
 * - createRole(array $data): RoleEntity
 *   Crea un nuevo rol con los datos proporcionados.
 *
 * - updateRole(array $data): bool
 *   Actualiza un rol existente con los datos proporcionados.
 *
 * - deleteRole(int $id): bool
 *   Elimina un rol por su identificador.
 */
class RoleService
{
    public function __construct(private RoleRepositoryInterface $roles) {}

    public function getRoleById(int $id): ?RoleEntity
    {
        return $this->roles->findById($id);
    }

    public function getAllRoles(): array
    {
        return $this->roles->findAll();
    }

    public function createRole(array $data): RoleEntity
    {
        return $this->roles->create($data);
    }

    public function updateRole(array $data): bool
    {
        return $this->roles->update($data);
    }

    public function deleteRole(int $id): bool
    {
        return $this->roles->delete($id);
    }
}
