<?php

namespace App\Modules\Roles\Domain\Entities;

/**
 * Entidad que representa un rol dentro del sistema.
 *
 * Esta clase define las propiedades y métodos asociados a los roles,
 * permitiendo la gestión de permisos y funcionalidades específicas
 * según el tipo de usuario.
 *
 * @package App\Modules\Roles\Domain\Entities
 */
class RoleEntity
{
    public function __construct(
        public ?string $id,
        public string $name,
        public ?string $description,
        public ?\DateTime $created_at = null,
        public ?\DateTime $updated_at = null
    ) {}
}
