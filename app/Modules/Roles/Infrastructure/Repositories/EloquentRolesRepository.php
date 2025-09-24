<?php

namespace App\Modules\Roles\Infrastructure\Repositories;

use App\Modules\Roles\Domain\Entities\RoleEntity;
use App\Modules\Roles\Domain\Repositories\RoleRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Repositorio Eloquent para la gestión de roles.
 *
 * Implementa la interfaz RoleRepositoryInterface para realizar operaciones CRUD sobre la tabla 'roles'.
 *
 * Métodos:
 * - findById(string $id): ?RoleEntity
 *   Busca un rol por su identificador. Retorna la entidad RoleEntity o null si no existe.
 *
 * - findAll(): array
 *   Obtiene todos los roles registrados en la base de datos. Retorna un arreglo de entidades RoleEntity.
 *
 * - create(array $role): RoleEntity
 *   Crea un nuevo rol con los datos proporcionados y retorna la entidad creada.
 *
 * - update(array $role): bool
 *   Actualiza los datos de un rol existente. Retorna true si la actualización fue exitosa.
 *
 * - delete(string $id): bool
 *   Elimina un rol por su identificador. Retorna true si la eliminación fue exitosa.
 *
 * Utiliza el Query Builder de Laravel para interactuar con la base de datos.
 */
class EloquentRolesRepository implements RoleRepositoryInterface
{
    public function findById(string $id): ?RoleEntity
    {
        $role = DB::table('roles')->where('id', $id)->first();
        if (! $role) {
            return null;
        }

        return new RoleEntity(
            id: $role->id,
            name: $role->name,
            description: $role->description,
            created_at: new \DateTime($role->created_at),
            updated_at: new \DateTime($role->updated_at)
        );
    }

    public function findAll(): array
    {
        $roles = DB::table('roles')->get();

        return array_map(fn ($role) => new RoleEntity(
            id: $role->id,
            name: $role->name,
            description: $role->description,
            created_at: new \DateTime($role->created_at),
            updated_at: new \DateTime($role->updated_at)
        ), $roles->toArray());
    }

    public function create(array $role): RoleEntity
    {
        $id = DB::table('roles')->insertGetId([
            'name' => $role['name'],
            'description' => $role['description'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this->findById($id);
    }

    public function update(array $role): bool
    {
        return DB::table('roles')->where('id', $role['id'])->update([
            'name' => $role['name'],
            'description' => $role['description'] ?? null,
            'updated_at' => now(),
        ]) > 0;
    }

    public function delete(string $id): bool
    {
        return DB::table('roles')->where('id', $id)->delete() > 0;
    }
}
