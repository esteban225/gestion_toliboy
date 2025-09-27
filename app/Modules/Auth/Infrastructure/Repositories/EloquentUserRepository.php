<?php

namespace App\Modules\Auth\Infrastructure\Repositories;

use App\Models\User as EloquentUser;
use App\Modules\Auth\Domain\Entities\UserEntity;
use App\Modules\Auth\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Log;

/**
 * Este código implementa varios principios SOLID:
 *
 * 1. Single Responsibility Principle (SRP): Cada clase o función tiene una única responsabilidad claramente definida, lo que facilita su mantenimiento y evolución.
 * 2. Open/Closed Principle (OCP): El código está diseñado para ser abierto a la extensión mediante herencia o composición, pero cerrado a la modificación directa, permitiendo agregar nuevas funcionalidades sin alterar el código existente.
 * 3. Liskov Substitution Principle (LSP): Las clases derivadas pueden sustituir a sus clases base sin alterar el comportamiento esperado del programa, asegurando la interoperabilidad.
 * 4. Interface Segregation Principle (ISP): Las interfaces están divididas según funcionalidades específicas, evitando que los clientes dependan de métodos que no utilizan.
 * 5. Dependency Inversion Principle (DIP): El código depende de abstracciones (interfaces o clases abstractas) en lugar de implementaciones concretas, facilitando la inyección de dependencias y la flexibilidad.
 *
 * Estos principios se implementan mediante el uso de clases bien definidas, interfaces, herencia y composición, promoviendo un diseño limpio, escalable y fácil de mantener.
 */
class EloquentUserRepository implements UserRepositoryInterface
{
    public function findByEmail(string $email): ?UserEntity
    {
        $user = EloquentUser::where('email', $email)->first();

        return $user ? $this->toEntity($user) : null;
    }

    public function findModelByEmail(string $email): ?EloquentUser
    {
        return EloquentUser::where('email', $email)->first();
    }

    public function findModelById(int $id): ?EloquentUser
    {
        return EloquentUser::find($id);
    }

    public function create(UserEntity $entity): UserEntity
    {

        $userArray = $entity->toArray();

        $user = EloquentUser::create($userArray);

        return $this->toEntity($user);
    }

    public function findById(int $id): ?UserEntity
    {
        $user = EloquentUser::find($id);

        return $user ? $this->toEntity($user) : null;
    }

    public function updateLastLogin(int $id, \DateTime $lastLogin): void
    {
        $user = EloquentUser::find($id);
        if (! $user) {
            return;
        }
        $user->last_login = $lastLogin->format('Y-m-d H:i:s');
        $user->save();
    }

    protected function toEntity(EloquentUser $user): UserEntity
    {
        return new UserEntity(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            password: $user->password,
            role_id: $user->role_id,
            position: $user->position,
            is_active: $user->is_active,
            last_login: $user->last_login
        );
    }
}
