<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'dev@example.com';
        $password = 'password'; // ⚠️ Cambiar en producción

        // Crear usuario si no existe
        $userId = DB::table('users')->where('email', $email)->value('id');
        if (! $userId) {
            $userId = DB::table('users')->insertGetId([
                'name' => 'Developer',
                'email' => $email,
                'password' => Hash::make($password),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Buscar id del rol DEV
        $roleId = DB::table('roles')->where('name', 'DEV')->value('id');

        if ($roleId) {
            $schema = DB::getSchemaBuilder();

            // Caso 1: existe tabla pivot role_user
            if ($schema->hasTable('role_user')) {
                $exists = DB::table('role_user')
                    ->where('role_id', $roleId)
                    ->where('user_id', $userId)
                    ->exists();

                if (! $exists) {
                    DB::table('role_user')->insert([
                        'role_id' => $roleId,
                        'user_id' => $userId,
                    ]);
                }

                // Caso 2: si la tabla users tiene columna role_id
            } elseif (Schema::hasColumn('users', 'role_id')) {
                DB::table('users')->where('id', $userId)->update([
                    'role_id' => $roleId,
                    'updated_at' => now(),
                ]);

                // Caso 3: compatibilidad con Spatie Laravel-Permission (model_has_roles)
            } elseif ($schema->hasTable('model_has_roles')) {
                $exists = DB::table('model_has_roles')
                    ->where('role_id', $roleId)
                    ->where('model_id', $userId)
                    ->where('model_type', 'App\\Models\\User')
                    ->exists();

                if (! $exists) {
                    DB::table('model_has_roles')->insert([
                        'role_id' => $roleId,
                        'model_type' => 'App\\Models\\User',
                        'model_id' => $userId,
                    ]);
                }

            } else {
                // fallback: informar
                $this->command->warn('⚠️ No se pudo asignar el rol DEV al usuario. Revisa tu esquema.');
            }
        }
    }
}
