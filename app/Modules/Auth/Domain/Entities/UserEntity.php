<?php

namespace App\Modules\Auth\Domain\Entities;

/**
 * Esta clase implementa varios principios SOLID:
 *
 * - Single Responsibility Principle (SRP): La clase User está diseñada para representar la entidad de usuario, manteniendo la responsabilidad enfocada en los datos y comportamientos relacionados con el usuario.
 * - Open/Closed Principle (OCP): La clase puede ser extendida para agregar nuevas funcionalidades sin modificar el código existente, permitiendo que esté abierta para extensión pero cerrada para modificación.
 * - Liskov Substitution Principle (LSP): Si existen subclases de User, estas pueden ser utilizadas en lugar de la clase base sin alterar el correcto funcionamiento del sistema.
 * - Interface Segregation Principle (ISP): Si la clase implementa interfaces, estas deben ser específicas y no forzar la implementación de métodos innecesarios.
 * - Dependency Inversion Principle (DIP): Si la clase depende de abstracciones (interfaces) en lugar de implementaciones concretas, facilita la flexibilidad y el desacoplamiento.
 */
class UserEntity
{
    public function __construct(
        public ?int $id,
        public string $name,
        public string $email,
        public string $password,
        public ?string $role_id,
        public ?string $status,
        public ?string $last_login = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}

    public function isActive(): bool
    {
        return $this->status === 1;
    }
}
