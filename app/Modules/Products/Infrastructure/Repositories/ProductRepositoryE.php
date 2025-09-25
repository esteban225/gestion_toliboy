<?php

namespace App\Modules\Products\Infrastructure\Repositories;

use App\Models\Product;
use App\Modules\Products\Domain\Entities\ProductEntity;
use App\Modules\Products\Domain\Repositories\ProductRepositoryI;

class ProductRepositoryE implements ProductRepositoryI
{
    public function all(array $filters = []): array
    {
        $query = Product::query();

        if (isset($filters['name'])) {
            $query->where('name', 'like', '%'.$filters['name'].'%');
        }

        if (isset($filters['code'])) {
            $query->where('code', 'like', '%'.$filters['code'].'%');
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        $products = $query->get();

        return $products->map(function ($product) {
            return new ProductEntity(
                id: $product->id,
                name: $product->name,
                code: $product->code,
                description: $product->description,
                price: (float) $product->price,
                image: $product->image,
                stock: (int) $product->stock,
                min_stock: (int) $product->min_stock,
                is_active: (bool) $product->is_active,
                created_by: $product->created_by
            );
        })->toArray();
    }

    public function find(string $id): ?ProductEntity
    {
        $product = Product::find($id);

        if (! $product) {
            return null;
        }

        return new ProductEntity(
            id: $product->id,
            name: $product->name,
            code: $product->code,
            description: $product->description,
            price: (float) $product->price,
            image: $product->image,
            stock: (int) $product->stock,
            min_stock: (int) $product->min_stock,
            is_active: (bool) $product->is_active,
            created_by: $product->created_by
        );
    }

    public function create(array $data): ?ProductEntity
    {
        $product = new Product;
        $product->name = $data['name'];
        $product->code = $data['code'];
        $product->description = $data['description'] ?? null;
        $product->price = $data['price'];
        $product->image = $data['image'] ?? null;
        $product->stock = $data['stock'] ?? 0;
        $product->min_stock = $data['min_stock'] ?? 0;
        $product->is_active = $data['is_active'] ?? true;
        $product->created_by = $data['created_by'];
        $product->save();

        return new ProductEntity(
            id: $product->id,
            name: $product->name,
            code: $product->code,
            description: $product->description,
            price: (float) $product->price,
            image: $product->image,
            stock: (int) $product->stock,
            min_stock: (int) $product->min_stock,
            is_active: (bool) $product->is_active,
            created_by: $product->created_by
        );
    }

    public function update(array $data): bool
    {
        $product = Product::find($data['id']);

        if (! $product) {
            return false;
        }

        $product->name = $data['name'] ?? $product->name;
        $product->code = $data['code'] ?? $product->code;
        $product->description = $data['description'] ?? $product->description;
        $product->price = $data['price'] ?? $product->price;
        $product->image = $data['image'] ?? $product->image;
        $product->stock = $data['stock'] ?? $product->stock;
        $product->min_stock = $data['min_stock'] ?? $product->min_stock;
        $product->is_active = $data['is_active'] ?? $product->is_active;

        return $product->save();
    }

    public function delete(string $id): bool
    {
        $product = Product::find($id);

        if (! $product) {
            return false;
        }

        return $product->delete();
    }
}
