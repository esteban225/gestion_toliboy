<?php

namespace App\Modules\Users\Aplication\UseCases;

use App\Modules\Users\Domain\Entities\UserEntity;
use App\Modules\Users\Domain\Services\UserService;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public function paginate(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->userService->paginateUsers($filters, $perPage);
    }

    /**
     * Obtener usuario por ID.
     *
     * @param  int  $id  Identificador único del usuario
     * @return mixed
     */
    public function get(int $id)
    {

        return $this->userService->getUser($id);

    }

    /**
     * Crear usuario.
     *
     * @param  UserEntity  $data  Datos del usuario (name, email, password, role_id, etc.)
     * @return mixed
     */
    public function create(UserEntity $data)
    {
        return $this->userService->createUser($data);
    }

    /**
     * Actualizar usuario.
     *
     * @param  int  $id  Identificador único del usuario
     * @param  UserEntity  $data  Datos actualizados del usuario
     */
    public function update(int $id, UserEntity $data): bool
    {
        $data->setId($id);

        return $this->userService->updateUser($data);
    }

    /**
     * Eliminar usuario.
     *
     * @param  int  $id  Identificador único del usuario
     */
    public function delete(int $id): bool
    {
        return $this->userService->deleteUser($id);
    }
}
