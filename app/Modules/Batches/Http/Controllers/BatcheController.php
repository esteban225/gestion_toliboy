<?php

namespace App\Modules\Batches\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Batches\Application\UseCases\BatcheUseCase;
use App\Modules\Batches\Http\Requests\RegisterRequest;
use App\Modules\Batches\Http\Requests\UpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Batches
 *
 * @description Endpoints para la gestión de lotes (batches).
 *
 * Controlador responsable de listar, mostrar, crear, actualizar y eliminar lotes.
 * Las respuestas están formateadas para Scramble/OpenAPI.
 */
class BatcheController extends Controller
{
    private BatcheUseCase $useCase;

    public function __construct(BatcheUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar lotes
     *
     * Obtiene una lista de lotes con filtros opcionales.
     *
     * @queryParam product_id int Opcional. Filtra por ID de producto. Example: 12
     * @queryParam status string Opcional. Filtra por estado del lote. Example: "processing"
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": "1", "product_id": 12, "quantity": 100, "status": "processing" }
     *   ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        $batches = $this->useCase->list($filters);

        return response()->json([
            'success' => true,
            'data' => $batches,
        ]);
    }

    /**
     * Mostrar lote
     *
     * Obtiene los detalles de un lote por su ID.
     *
     * @urlParam id string required ID del lote. Example: "1"
     *
     * @response 200 {
     *   "success": true,
     *   "data": { "id": "1", "product_id": 12, "quantity": 100, "status": "processing" }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Batche not found"
     * }
     */
    public function show(string $id): JsonResponse
    {
        $batche = $this->useCase->find($id);
        if (! $batche) {
            return response()->json(['success' => false, 'message' => 'Batche not found'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $batche,
        ]);
    }

    /**
     * Crear lote
     *
     * Registra un nuevo lote en el sistema.
     *
     * @bodyParam product_id int required ID del producto. Example: 12
     * @bodyParam quantity number required Cantidad del lote. Example: 100
     * @bodyParam production_date date Opcional. Fecha de producción. Example: "2025-09-24"
     *
     * @response 201 {
     *   "success": true,
     *   "data": { "id": "2", "product_id": 12, "quantity": 100, "status": "created" },
     *   "message": "Batche created"
     * }
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $batche = $this->useCase->create($data);

        return response()->json([
            'success' => true,
            'data' => $batche,
            'message' => 'Batche created',
        ], 201);
    }

    /**
     * Actualizar lote
     *
     * Actualiza los datos de un lote existente.
     *
     * @urlParam id string required ID del lote. Example: "1"
     *
     * @bodyParam status string Opcional. Nuevo estado del lote. Example: "finished"
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Batche updated successfully"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Batche not found or not updated"
     * }
     */
    public function update(UpdateRequest $request, string $id): JsonResponse
    {
        $data = array_merge(['id' => $id], $request->all());
        $updated = $this->useCase->update($data);

        if (! $updated) {
            return response()->json(['success' => false, 'message' => 'Batche not found or not updated'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Batche updated successfully']);
    }

    /**
     * Eliminar lote
     *
     * Elimina un lote por su ID.
     *
     * @urlParam id string required ID del lote. Example: "1"
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Batche deleted successfully"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Batche not found or not deleted"
     * }
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->useCase->delete($id);

        if (! $deleted) {
            return response()->json(['success' => false, 'message' => 'Batche not found or not deleted'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Batche deleted successfully']);
    }
}
