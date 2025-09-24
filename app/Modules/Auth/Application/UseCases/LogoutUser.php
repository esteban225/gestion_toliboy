<?php

namespace App\Modules\Auth\Application\UseCases;

use App\Modules\Auth\Domain\Services\AuthService;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

/**
 * Este código implementa varios principios SOLID:
 *
 * 1. Single Responsibility Principle (SRP): Cada clase o función tiene una única responsabilidad, facilitando su mantenimiento y evolución.
 * 2. Open/Closed Principle (OCP): El código está diseñado para ser extendido sin modificar su estructura original, permitiendo agregar nuevas funcionalidades mediante herencia o composición.
 * 3. Liskov Substitution Principle (LSP): Las clases derivadas pueden sustituir a sus clases base sin alterar el comportamiento esperado del programa.
 * 4. Interface Segregation Principle (ISP): Las interfaces están divididas según funcionalidades específicas, evitando que las clases dependan de métodos que no utilizan.
 * 5. Dependency Inversion Principle (DIP): Las dependencias se gestionan a través de abstracciones (interfaces), desacoplando el código y facilitando la inyección de dependencias.
 */
class LogoutUser
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle()
    {
        try {
            JWTAuth::parseToken()->invalidate();

            return true;
        } catch (JWTException $e) {
            Log::error('"UseCase.LogoutUser": Error al cerrar sesión: '.$e->getMessage());

            return false;
        }
    }
}
