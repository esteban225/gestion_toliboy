<?php

namespace App\Modules\Inventory\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Inventory\UseCases\InventoryUseCase;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(private InventoryUseCase $useCase) {}

    public function products(Request $request)
    {
        return $this->useCase->listProducts($request->query());
    }

    public function product(string $id)
    {
        return $this->useCase->getProduct($id);
    }

    public function rawMaterials(Request $request)
    {
        return $this->useCase->listRawMaterials($request->query());
    }

    public function batches(Request $request)
    {
        return $this->useCase->listBatches($request->query());
    }

    public function inventoryMovements(Request $request)
    {
        return $this->useCase->listMovements($request->query());
    }
}
