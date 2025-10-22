<?php

namespace App\Modules\Auth\Domain\Services;

use App\Modules\Auth\Domain\Entities\UserEntity;
use App\Modules\Auth\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
/**
 * Principios SOLID implementados en este archivo:
 *
 * 1. Single Responsibility Principle (SRP):
 *    - Cada clase y método dentro de este servicio de autenticación tiene una única responsabilidad relacionada con la autenticación de usuarios.
 *    - Por ejemplo, la clase AuthService se encarga exclusivamente de la lógica de autenticación, delegando otras responsabilidades a clases externas.
 *
 * 2. Open/Closed Principle (OCP):
 *    - El servicio está diseñado para ser extendido sin modificar su código base, permitiendo agregar nuevas funcionalidades de autenticación mediante herencia o composición.
 *
 * 3. Liskov Substitution Principle (LSP):
 *    - Las dependencias y servicios utilizados pueden ser reemplazados por implementaciones que respeten los contratos definidos, sin afectar el funcionamiento del sistema.
 *
 * 4. Interface Segregation Principle (ISP):
 *    - Se utilizan interfaces específicas para cada tipo de servicio o repositorio, evitando la dependencia de métodos innecesarios.
 *
 * 5. Dependency Inversion Principle (DIP):
 *    - El servicio depende de abstracciones (interfaces) en lugar de implementaciones concretas, facilitando la inyección de dependencias y la escalabilidad del sistema.
 */
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(UserEntity $data)
    {
        $data->setPassword(Hash::make($data->getPassword()));
        $userEntity = $this->userRepository->create($data);

        return $userEntity;
    }

    public function login(string $email, string $password): ?array
    {
        $userModel = $this->userRepository->findModelByEmail($email);

        if (! $userModel || ! Hash::check($password, $userModel->password)) {
            return null;
        }

        // Actualizamos fecha de último acceso
        $this->userRepository->updateLastLogin($userModel->id, now());

        // Generamos token
        $token = JWTAuth::fromUser($userModel);

        // Retornamos token + usuario
        return [
            'token' => $token,
            'user' => $userModel->load('role'), // carga la relación role
        ];
    }

}
