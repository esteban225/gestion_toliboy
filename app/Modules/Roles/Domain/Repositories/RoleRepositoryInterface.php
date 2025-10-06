<?php

namespace App\Modules\Roles\Domain\Repositories;

use App\Modules\Roles\Domain\Entities\RoleEntity;

/**
 * Interface RoleRepositoryInterface
 *
 * Define los métodos para la gestión de roles en el repositorio.
 *
 * Métodos:
 * - findById(int $id): ?RoleEntity
 *      Busca y retorna una entidad de rol por su identificador único.
 *      Retorna null si no se encuentra el rol.
 *
 * - findAll(): array
 *      Retorna un arreglo con todas las entidades de rol existentes.
 *
 * - create(array $role): RoleEntity
 *      Crea un nuevo rol con los datos proporcionados y retorna la entidad creada.
 *
 * - update(array $role): bool
 *      Actualiza los datos de un rol existente.
 *      Retorna true si la actualización fue exitosa, false en caso contrario.
 *
 * - delete(int $id): bool
 *      Elimina el rol identificado por el ID proporcionado.
 *      Retorna true si la eliminación fue exitosa, false en caso contrario.
 */
interface RoleRepositoryInterface
{
    public function findById(int $id): ?RoleEntity;

    public function findAll(): array;

    public function create(array $role): RoleEntity;

    public function update(array $role): bool;

    public function delete(int $id): bool;
}
