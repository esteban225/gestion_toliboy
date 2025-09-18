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
        $password = 'password'; // cambiar en producción

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

            // Si existe tabla pivot role_user -> insertar si no existe
            if ($schema->hasTable('role_user')) {
                $exists = DB::table('role_user')->where('role_id', $roleId)->where('user_id', $userId)->exists();
                if (! $exists) {
                    DB::table('role_user')->insert([
                        'role_id' => $roleId,
                        'user_id' => $userId,
                    ]);
                }
            } elseif (Schema::hasColumn('users', 'role_id')) {
                // Si users tiene columna role_id -> actualizar
                DB::table('users')->where('id', $userId)->update(['role_id' => $roleId, 'updated_at' => now()]);
            } else {
                // fallback: crear registro en role_user (si no existe la tabla, crearla no es automático aquí)
                // No hacemos más para evitar crear tablas sin tu confirmación.
            }
        }
    }
}
