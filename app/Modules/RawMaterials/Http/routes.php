<?php

use App\Modules\RawMaterials\Http\Controllers\RawMaterialsController;
use Illuminate\Support\Facades\Route;

Route::prefix('raw-materials')->middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {

    Route::get('/', [RawMaterialsController::class, 'index']);
    Route::get('/{id}', [RawMaterialsController::class, 'show']);
    Route::post('/', [RawMaterialsController::class, 'store']);
    Route::put('/{id}', [RawMaterialsController::class, 'update']);
    Route::delete('/{id}', [RawMaterialsController::class, 'destroy']);
});
