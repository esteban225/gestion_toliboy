<?php

namespace App\Modules\Users\Domain\Entities;

/**
 * Entidad de dominio para usuario.
 *
 * Representa los datos y reglas de negocio de un usuario en el sistema.
 *
 * Principios SOLID aplicados:
 * - SRP: Solo gestiona datos y comportamientos del usuario.
 * - OCP: Puede extenderse sin modificar el código base.
 * - LSP: Subclases pueden sustituir esta clase sin romper el sistema.
 * - ISP: Si implementa interfaces, deben ser específicas.
 * - DIP: Puede depender de abstracciones para mayor flexibilidad.
 */
class UserEntity
{
    /**
     * @param  int|null  $id  Identificador único del usuario
     * @param  string  $name  Nombre del usuario
     * @param  string  $email  Correo electrónico del usuario
     * @param  string  $password  Contraseña del usuario (hash)
     * @param  string|null  $role_id  Identificador del rol
     * @param  string|null  $is_active  Estado activo/inactivo (1/0)
     * @param  string|null  $last_login  Fecha/hora del último acceso
     * @param  string|null  $createdAt  Fecha/hora de creación
     * @param  string|null  $updatedAt  Fecha/hora de última actualización
     */
    public function __construct(
        public ?int $id,
        public string $name,
        public string $email,
        public string $password,
        public ?string $role_id,
        public ?string $is_active,
        public ?string $last_login = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    /**
     * Indica si el usuario está activo.
     */
    public function isActive(): bool
    {
        return $this->is_active === '1' || $this->is_active === 1;
    }
}
