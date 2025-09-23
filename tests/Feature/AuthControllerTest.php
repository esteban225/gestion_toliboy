<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;

test('puede registrar un usuario', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test2@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'success',
            'token',
            'user' => ['id', 'name', 'email']
        ])
        ->assertJson(['success' => true]);

    $this->assertDatabaseHas('users', ['email' => 'test2@example.com']);
});

test('falla el registro con email duplicado', function () {
    User::factory()->create(['email' => 'test2@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test2@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(422)
        ->assertJson(['success' => false]);
});

test('puede hacer login con credenciales válidas', function () {
    $user = User::factory()->create([
        'email' => 'test2@example.com',
        'password' => Hash::make('password123')
    ]);

    $response = $this->postJson('/api/login', [
        'email' => 'test2@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'success',
            'token',
            'user' => ['id', 'name', 'email']
        ])
        ->assertJson(['success' => true]);
});

test('falla el login con credenciales inválidas', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'fake@example.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertStatus(401)
        ->assertJson(['success' => false]);
});

test('puede cerrar sesión con token válido', function () {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->postJson('/api/logout');

    $response->assertStatus(200)
        ->assertJson(['message' => 'Sesión cerrada correctamente']);
});

test('puede refrescar el token', function () {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->postJson('/api/refresh');

    $response->assertStatus(200)
        ->assertJsonStructure(['access_token', 'token_type', 'expires_in']);
});

test('puede obtener el usuario actual', function () {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token
    ])->getJson('/api/me');

    $response->assertStatus(200)
        ->assertJson(['id' => $user->id, 'email' => $user->email]);
});

test('no puede acceder a rutas protegidas sin token', function () {
    $response = $this->getJson('/api/me');

    $response->assertStatus(401);
});
