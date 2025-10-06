<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Support\Facades\Route;

Scramble::registerUiRoute('/');
Scramble::registerJsonSpecificationRoute('api.json');

// Rutas para la documentación automática de la API con Scramble
// Puedes cambiar 'docs.example.com' por el dominio donde quieras servir la documentación
// Route::get('docs.example.com')->group(function () {
//     Scramble::registerUiRoute('api');
//     Scramble::registerJsonSpecificationRoute('api.json');
// });
