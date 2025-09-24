<?php

namespace App\Modules\Inventory\UseCases;

use App\Modules\Inventory\Infrastructure\Repositories\InventoryRepository;
use Illuminate\Http\JsonResponse;

class InventoryUseCase
{
    public function __construct(private InventoryRepository $repo) {}

    public function listProducts(array $filters): JsonResponse
    {
        return response()->json($this->repo->products($filters));
    }

    public function getProduct(string $id): JsonResponse
    {
        return response()->json($this->repo->findProduct($id));
    }

    public function listRawMaterials(array $filters): JsonResponse
    {
        return response()->json($this->repo->rawMaterials($filters));
    }

    public function listBatches(array $filters): JsonResponse
    {
        return response()->json($this->repo->batches($filters));
    }

    public function listMovements(array $filters): JsonResponse
    {
        return response()->json($this->repo->movements($filters));
    }
}
