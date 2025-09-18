<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['DEV', 'GG', 'INPL', 'INPR', 'TRZ', 'OP'];
        $now = now();

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['name' => $role],
            );
        }
    }
}
