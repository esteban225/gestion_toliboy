<?php

namespace App\Modules\Products\Infrastructure\Repositories;

use App\Models\Product;
use App\Modules\Products\Domain\Entities\ProductEntity;
use App\Modules\Products\Domain\Repositories\ProductRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductRepositoryE implements ProductRepositoryI
{
    public function all(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = Product::query();
        foreach ($filters as $key => $value) {
            $query->where($key, $value);
        }

        return $query->paginate($perPage);
    }

    public function find(int $id): ?ProductEntity
    {
        $product = Product::find($id);

        if (! $product) {
            return null;
        }

        return $this->mapToEntity($product);
    }

    public function create(ProductEntity $entity): ?ProductEntity
    {
        $productModel = Product::create($entity->toArray());
        $productModel->refresh();

        return $this->mapToEntity($productModel);
    }

    public function update(ProductEntity $entity): bool
    {
        Log::info('Updating product with ID: '.$entity->getId());
        $product = Product::find($entity->getId());

        if ($product) {
            return $product->update($entity->toArray());
        }

        return false;

    }

    public function delete(int $id): bool
    {
        $product = Product::find($id);

        if (! $product) {
            return false;
        }

        return $product->delete();
    }

    private function mapToEntity(Product $product): ProductEntity
    {
        return new ProductEntity(
            $product->id,
            $product->name,
            $product->code,
            $product->category,
            $product->description,
            $product->specifications,
            $product->unit_price,
            $product->is_active,
            $product->created_by
        );
    }
}
