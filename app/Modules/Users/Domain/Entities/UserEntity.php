<?php

namespace App\Modules\Users\Domain\Entities;

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
    private ?int $id;

    private string $name;

    private string $email;

    private string $password;

    private ?string $role_id;

    private ?string $position;

    private ?bool $is_active;

    private ?string $last_login;

    public function __construct(
        ?int $id,
        string $name,
        string $email,
        string $password,
        ?string $role_id,
        ?string $position,
        ?bool $is_active,
        ?string $last_login,

    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->position = $position;
        $this->is_active = $is_active;
        $this->last_login = $last_login;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRoleId(): ?string
    {
        return $this->role_id;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function getIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function getLastLogin(): ?string
    {
        return $this->last_login;
    }

    // Setters

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setRoleId(?string $role_id): void
    {
        $this->role_id = $role_id;
    }

    public function setPosition(?string $position): void
    {
        $this->position = $position;
    }

    public function setIsActive(?bool $is_active): void
    {
        $this->is_active = $is_active;
    }

    public function setLastLogin(?string $last_login): void
    {
        $this->last_login = $last_login;
    }

    // Domain behaviour
    public function isActive(): bool
    {
        return $this->is_active === 1;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            role_id: $data['role_id'] ?? null,
            position: $data['position'] ?? null,
            is_active: $data['is_active'] ?? true,
            last_login: $data['last_login'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role_id' => $this->role_id,
            'position' => $this->position,
            'is_active' => $this->is_active,
            'last_login' => $this->last_login,
        ];
    }
}
