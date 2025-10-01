<?php

use App\Modules\RawMaterials\Http\Controllers\RawMaterialsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'jwt.auth', 'role:DEV,GG,INGPL,INGPR', \App\Http\Middleware\SetDbSessionUser::class])->group(function () {

    Route::get('/raw-materials', [RawMaterialsController::class, 'index']);
    Route::get('/raw-materials/{id}', [RawMaterialsController::class, 'show']);
    Route::post('/raw-materials', [RawMaterialsController::class, 'store']);
    Route::put('/raw-materials/{id}', [RawMaterialsController::class, 'update']);
    Route::delete('/raw-materials/{id}', [RawMaterialsController::class, 'destroy']);
});
