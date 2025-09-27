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
    private ?int $id;

    private string $name;

    private string $email;

    private string $password;

    private ?string $role_id;

    private ?bool $status;

    private ?string $last_login;

    public function __construct(
        ?int $id,
        string $name,
        string $email,
        string $password,
        ?string $role_id,
        ?bool $status,
        ?string $last_login,

    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role_id = $role_id;
        $this->status = $status;
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

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function getLastLogin(): ?string
    {
        return $this->last_login;
    }

    // Setters
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

    public function setStatus(?bool $status): void
    {
        $this->status = $status;
    }

    public function setLastLogin(?string $last_login): void
    {
        $this->last_login = $last_login;
    }

    // Domain behaviour
    public function isActive(): bool
    {
        return $this->status === 1;
    }
}
