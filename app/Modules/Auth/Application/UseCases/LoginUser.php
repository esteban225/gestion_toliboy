<?php

namespace App\Modules\Auth\Application\UseCases;

use App\Modules\Auth\Domain\Services\AuthService;

/**
 * Este código implementa varios principios SOLID:
 *
 * S - Principio de Responsabilidad Única (SRP): Cada clase/método tiene una única responsabilidad claramente definida.
 * O - Principio de Abierto/Cerrado (OCP): Las clases están diseñadas para ser extendidas sin modificar su código fuente.
 * L - Principio de Sustitución de Liskov (LSP): Las clases derivadas pueden sustituir a sus clases base sin alterar el comportamiento esperado.
 * I - Principio de Segregación de Interfaces (ISP): Las interfaces están divididas según funcionalidades específicas, evitando métodos innecesarios.
 * D - Principio de Inversión de Dependencias (DIP): Las dependencias se abstraen mediante interfaces, permitiendo una fácil inyección y desacoplamiento.
 */
class LoginUser
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function handle(string $email, string $password)
    {
        return $this->authService->login($email, $password);
    }
}
