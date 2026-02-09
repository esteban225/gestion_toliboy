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
        $usuarios = [
            [
                'name' => 'Developer',
                'email' => 'desarrollo@toliboy.com',
                'password' => 'DevPassword',
                'role' => 'DEV',
            ],
            [
                'name' => 'Trazabilidad',
                'email' => 'trazabilidad@toliboy.com',
                'password' => 'trpassword',
                'role' => 'TRZ',

            ],
            [
                'name' => 'Operador',
                'email' => 'operador@toliboy.com',
                'password' => 'opassword',
                'role' => 'OP',

            ],
        ];

        $schema = DB::getSchemaBuilder();
        $roles = DB::table('roles')
            ->whereIn('name', array_unique(array_column($usuarios, 'role')))
            ->pluck('id', 'name')
            ->all();

        foreach ($usuarios as $u) {
            $roleId = $roles[$u['role']] ?? null;

            // Crear usuario si no existe
            $userId = DB::table('users')->where('email', $u['email'])->value('id');

            if (! $userId) {
                $userId = DB::table('users')->insertGetId([
                    'name' => $u['name'],
                    'email' => $u['email'],
                    'password' => Hash::make($u['password']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Asignar el rol configurado para el usuario
            if ($roleId) {

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
                } elseif ($schema->hasColumn('users', 'role_id')) {

                    DB::table('users')->where('id', $userId)->update([
                        'role_id' => $roleId,
                    ]);
                } elseif ($schema->hasTable('model_has_roles')) {

                    $exists = DB::table('model_has_roles')
                        ->where('role_id', $roleId)
                        ->where('model_id', $userId)
                        ->exists();

                    if (! $exists) {
                        DB::table('model_has_roles')->insert([
                            'role_id' => $roleId,
                            'model_type' => 'App\\Models\\User',
                            'model_id' => $userId,
                        ]);
                    }
                }
            }
        }
    }
}
