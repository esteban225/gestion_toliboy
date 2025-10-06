<?php

use Dedoc\Scramble\Scramble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// 👇 Ruta personalizada para la documentación
Route::prefix('docs/api')->group(function () {
    Scramble::registerUiRoute('/');
    Scramble::registerJsonSpecificationRoute('api.json');
});


Route::get('/', function () {
    return view('auth.login-docs');
})->name('login');

Route::get('/docs', function () {
    return view('docs.index'); // contiene el botón y un iframe a /docs/api
})->middleware(['auth', 'can:viewApiDocs']);

/**
 * Procesar el login.
 */
Route::post('/login', function (Request $request) {
    $credentials = $request->only('email', 'password');

    if (Auth::attempt($credentials)) {
        $user = Auth::user();

        // Solo permitir el correo autorizado
        if ($user->email === 'desarrollo@toliboy.com') {
            return redirect('/docs');
        }

        Auth::logout();
        return back()->withErrors(['email' => 'No tienes permisos para acceder.']);
    }

    return back()->withErrors(['email' => 'Credenciales inválidas.']);
});

/**
 * Cerrar sesión
 */
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');

// Rutas para la documentación automática de la API con Scramble
// Puedes cambiar 'docs.example.com' por el dominio donde quieras servir la documentación
// Route::get('docs.example.com')->group(function () {
//     Scramble::registerUiRoute('api');
//     Scramble::registerJsonSpecificationRoute('api.json');
// });
