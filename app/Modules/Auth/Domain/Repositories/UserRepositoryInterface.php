<?php

namespace App\Modules\Auth\Domain\Repositories;

use App\Models\User as EloquentUser;
use App\Modules\Auth\Domain\Entities\UserEntity;

/**
 * Principios SOLID implementados:
 *
 * 1. Single Responsibility Principle (SRP):
 *    Cada clase y método en el código tiene una única responsabilidad, facilitando su mantenimiento y comprensión.
 *
 * 2. Open/Closed Principle (OCP):
 *    El diseño permite extender funcionalidades sin modificar el código existente, por ejemplo, mediante el uso de interfaces o clases abstractas.
 *
 * 3. Liskov Substitution Principle (LSP):
 *    Las clases derivadas pueden sustituir a sus clases base sin alterar el funcionamiento del programa.
 *
 * 4. Interface Segregation Principle (ISP):
 *    Las interfaces están diseñadas para ser específicas, evitando que las clases implementen métodos que no utilizan.
 *
 * 5. Dependency Inversion Principle (DIP):
 *    El código depende de abstracciones (interfaces o clases abstractas) y no de implementaciones concretas, facilitando la flexibilidad y el testeo.
 */
interface UserRepositoryInterface
{
    public function create(array $data): UserEntity;

    public function findByEmail(string $email): ?UserEntity;

    public function findModelByEmail(string $email): ?EloquentUser;

    public function findModelById(int $id): ?EloquentUser;

    public function findById(int $id): ?UserEntity;

    public function updateLastLogin(int $id, \DateTime $lastLogin): void;
}
