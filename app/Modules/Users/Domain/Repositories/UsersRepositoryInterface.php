<?php

namespace App\Modules\Users\Domain\Repositories;

use App\Models\User;
use App\Modules\Users\Domain\Entities\UserEntity;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Contrato para el repositorio de usuarios.
 *
 * Define los métodos que cualquier implementación de repositorio de usuarios debe proveer,
 * permitiendo desacoplar la lógica de acceso a datos de la lógica de negocio.
 *
 * Principios SOLID aplicados:
 * - SRP: Solo define la interfaz para persistencia de usuarios.
 * - OCP: Puede extenderse con nuevas implementaciones sin modificar el contrato.
 * - LSP: Cualquier implementación puede sustituir esta interfaz sin romper el sistema.
 * - ISP: La interfaz es específica para usuarios.
 * - DIP: Los servicios y casos de uso dependen de esta abstracción, no de una implementación concreta.
 */
interface UsersRepositoryInterface
{
    /**
     * Obtiene todos los usuarios, opcionalmente filtrados.
     *
     * @param  array  $filters  Filtros de búsqueda (ej: ['role_id' => 2])
     * @return UserEntity[]|array Lista de entidades de usuario
     */
    public function all(array $filters = []): array;

    /**
     * Obtiene datos de usuario paginados.
     *
     * @param  array  $filters  Filtros de búsqueda
     * @param  int  $perPage  Cantidad por página
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Busca un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return UserEntity|null Entidad de usuario o null si no existe
     */
    public function find(int $id): ?UserEntity;

    /**
     * Crea un nuevo usuario.
     *
     * @param  UserEntity  $data  Datos del usuario (name, email, password, role_id, etc.)
     * @return UserEntity|null Entidad creada o null si falla
     */
    public function create(UserEntity $data): ?UserEntity;

    /**
     * Actualiza un usuario existente.
     *
     * @param  UserEntity  $data  Datos actualizados del usuario (debe incluir el id)
     * @return UserEntity |null Entidad actualizada o null si falla
     */
    public function update(UserEntity $data): ?UserEntity;

    /**
     * Elimina un usuario por su identificador.
     *
     * @param  int  $id  Identificador único del usuario
     * @return bool True si la eliminación fue exitosa, false en caso contrario
     */
    public function delete(int $id): bool;
}
