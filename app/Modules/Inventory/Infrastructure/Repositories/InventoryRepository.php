<?php

namespace App\Modules\Inventory\Infrastructure\Repositories;

use Illuminate\Support\Facades\DB;

class InventoryRepository
{
    public function products(array $filters = []): array
    {
        $q = DB::table('products');
        if (!empty($filters['q'])) $q->where('name','like',"%{$filters['q']}%");
        return $q->get()->toArray();
    }

    public function findProduct(string $id) { return DB::table('products')->where('id',$id)->first(); }

    public function rawMaterials(array $filters = []): array
    {
        $q = DB::table('raw_materials');
        return $q->get()->toArray();
    }

    public function batches(array $filters = []): array
    {
        return DB::table('batches')->limit(1000)->get()->toArray();
    }

    public function movements(array $filters = []): array
    {
        return DB::table('inventory_movements')->limit(1000)->get()->toArray();
    }
}
