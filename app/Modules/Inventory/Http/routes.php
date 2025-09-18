<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Inventory\Http\Controllers\InventoryController;

Route::middleware(['api','jwt.auth','role:INPL,INPR,TRZ,DEV', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {
    Route::get('products', [InventoryController::class,'products']);
    Route::get('products/{id}', [InventoryController::class,'product']);
    Route::get('raw-materials', [InventoryController::class,'rawMaterials']);
    Route::get('batches', [InventoryController::class,'batches']);
    Route::get('inventory-movements', [InventoryController::class,'inventoryMovements']);
});
