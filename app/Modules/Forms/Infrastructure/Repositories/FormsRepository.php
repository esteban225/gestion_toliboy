<?php

namespace App\Modules\Forms\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;

class FormsRepository
{
    public function all(array $filters): array
    {
        $q = DB::table('forms');
        if (!empty($filters['q'])) $q->where('title', 'like', "%{$filters['q']}%");
        return $q->get()->toArray();
    }

    public function find(string $id)
    {
        return DB::table('forms')->where('id', $id)->first();
    }

    public function create(array $data): int
    {
        return DB::table('forms')->insertGetId(array_merge($data, ['created_at'=>now(),'updated_at'=>now()]));
    }

    public function update(string $id, array $data): int
    {
        return DB::table('forms')->where('id', $id)->update(array_merge($data, ['updated_at'=>now()]));
    }

    public function delete(string $id): int
    {
        return DB::table('forms')->where('id', $id)->delete();
    }
}
