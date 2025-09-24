<?php

namespace App\Modules\Users\Aplication\UseCases;

use App\Modules\Users\Domain\Services\UserService;

/**
 * Caso de uso para la gestión de usuarios.
 *
 * Orquesta las operaciones de listado, consulta, creación, actualización y eliminación de usuarios,
 * delegando la lógica de negocio al UserService.
 *
 * Principios SOLID aplicados:
 * - SRP: Cada método tiene una única responsabilidad (listar, obtener, crear, actualizar, eliminar).
 * - OCP: Puede extenderse con nuevos métodos sin modificar los existentes.
 * - LSP: Puede ser sustituido por otra implementación de caso de uso sin romper el sistema.
 * - ISP: Expone solo los métodos necesarios para la gestión de usuarios.
 * - DIP: Depende de la abstracción UserService, no de una implementación concreta.
 */
class ManageUserUseCase
{
    /**
     * @var UserService Servicio de usuarios
     */
    public function __construct(private UserService $userService) {}

    /**
     * Listar usuarios con filtros.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['role_id' => 2])
     */
    public function list(array $filters): array
    {
        return $this->userService->listUsers($filters);
    }

    /**
     * Obtener usuario por ID.
     *
     * @param  string  $id  Identificador único del usuario
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->userService->getUser($id);
    }

    /**
     * Crear usuario.
     *
     * @param  array  $data  Datos del usuario (name, email, password, role_id, etc.)
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->userService->createUser($data);
    }

    /**
     * Actualizar usuario.
     *
     * @param  string  $id  Identificador único del usuario
     * @param  array  $data  Datos actualizados del usuario
     */
    public function update(string $id, array $data): bool
    {
        return $this->userService->updateUser(array_merge($data, ['id' => $id]));
    }

    /**
     * Eliminar usuario.
     *
     * @param  string  $id  Identificador único del usuario
     */
    public function delete(string $id): bool
    {
        return $this->userService->deleteUser($id);
    }
}
