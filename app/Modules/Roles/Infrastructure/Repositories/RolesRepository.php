<?php

namespace App\Modules\Roles\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;

class RolesRepository
{
    public function all(array $filters = []): array { return DB::table('roles')->get()->toArray(); }
    public function create(array $data): int { return DB::table('roles')->insertGetId(array_merge($data,['created_at'=>now(),'updated_at'=>now()])); }
    public function update(string $id, array $data): int { return DB::table('roles')->where('id',$id)->update(array_merge($data,['updated_at'=>now()])); }
}
