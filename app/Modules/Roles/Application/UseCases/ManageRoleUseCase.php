<?php

namespace App\Modules\Roles\Application\UseCases;

use App\Modules\Roles\Domain\Services\RoleService;

/**
 * Caso de uso para la gestión de roles.
 *
 * Proporciona métodos para obtener, listar, crear, actualizar y eliminar roles.
 *
 * Métodos:
 * - __construct(RoleService $service): Inicializa el caso de uso con el servicio de roles.
 * - getById(int $id): Obtiene un rol por su identificador.
 * - list(array $filters): Lista todos los roles (los filtros no se utilizan actualmente).
 * - create(array $data): Crea un nuevo rol con los datos proporcionados.
 * - update(int $id, array $data): Actualiza un rol existente con el identificador y los datos proporcionados.
 * - delete(int $id): Elimina un rol por su identificador.
 */
class ManageRoleUseCase
{
    public function __construct(private RoleService $service) {}

    public function getById(int $id)
    {
        return $this->service->getRoleById($id);
    }

    public function list(array $filters)
    {
        return $this->service->getAllRoles();
    }

    public function create(array $data)
    {
        return $this->service->createRole($data);
    }

    public function update(int $id, array $data)
    {
        return $this->service->updateRole(array_merge($data, ['id' => $id]));
    }

    public function delete(int $id)
    {
        return $this->service->deleteRole($id);
    }
}
