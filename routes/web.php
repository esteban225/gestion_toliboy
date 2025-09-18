<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('api/docs', function () {
    return view('api.docs'); // Asegúrate de tener una vista para la documentación
});

Route::get('docs', function () {
    return view('api.docs'); // Documentación de la API
});

Route::post('example', function () {
    return response()->json(['message' => 'CSRF bypassed']);
});
