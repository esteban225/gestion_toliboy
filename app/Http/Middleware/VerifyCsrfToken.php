<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Excluir rutas específicas de CSRF
        'api/docs/*', // Ajusta esta ruta según tu configuración
        'api/*',      // Si quieres excluir todas las rutas de la API
    ];
}
