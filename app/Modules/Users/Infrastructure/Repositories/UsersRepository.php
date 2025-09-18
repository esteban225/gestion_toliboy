<?php

namespace App\Modules\Users\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersRepository
{
    public function all(array $filters = []): array
    {
        $q = DB::table('users');
        if (! empty($filters['q'])) $q->where('name','like',"%{$filters['q']}%");
        return $q->get()->toArray();
    }

    public function find(string $id) { return DB::table('users')->where('id',$id)->first(); }

    public function create(array $data): int
    {
        $data['password'] = isset($data['password']) ? Hash::make($data['password']) : Hash::make('password');
        return DB::table('users')->insertGetId(array_merge($data,['created_at'=>now(),'updated_at'=>now()]));
    }

    public function update(string $id, array $data): int
    {
        if (isset($data['password'])) $data['password'] = Hash::make($data['password']);
        return DB::table('users')->where('id',$id)->update(array_merge($data,['updated_at'=>now()]));
    }

    public function delete(string $id): int { return DB::table('users')->where('id',$id)->delete(); }
}
