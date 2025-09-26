<?php

namespace App\Modules\InventoryMovements\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\InventoryMovements\Application\UseCases\InvMoveUseCase;
use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;
use App\Modules\InventoryMovements\Http\Requests\RegisterRequest;
use App\Modules\InventoryMovements\Http\Requests\UpDateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @class InvMoveController
 *
 * Controlador HTTP para la gestión de movimientos de inventario.
 * Implementa operaciones CRUD usando el caso de uso InvMoveUseCase.
 */
class InvMoveController extends Controller
{
    private InvMoveUseCase $useCase;

    /**
     * Constructor del controlador
     *
     * @param  InvMoveUseCase  $useCase  Caso de uso para manejar movimientos de inventario
     */
    public function __construct(InvMoveUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar todos los movimientos de inventario
     *
     * @param  Request  $request  Filtros opcionales enviados en la petición
     * @return JsonResponse Lista de movimientos de inventario
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        $invMoves = $this->useCase->list($filters);

        return response()->json($invMoves);
    }

    /**
     * Mostrar un movimiento de inventario específico por ID
     *
     * @param  string  $id  Identificador del movimiento de inventario
     * @return JsonResponse Datos del movimiento de inventario o error 404 si no existe
     */
    public function show(string $id): JsonResponse
    {
        $invMove = $this->useCase->find($id);
        if ($invMove) {
            return response()->json($invMove);
        }

        return response()->json(['message' => 'Inventory Movement not found'], 404);
    }

    /**
     * Registrar un nuevo movimiento de inventario
     *
     * @param  RegisterRequest  $request  Datos del nuevo movimiento de inventario validados
     * @return JsonResponse Datos del movimiento de inventario creado o error 400 si falla
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = InvMoveEntity::fromArray($request->validated());
        $invMove = $this->useCase->create($data);
        if ($invMove) {
            return response()->json($invMove, 201);
        }

        return response()->json(['message' => 'Failed to create Inventory Movement'], 400);
    }

    /**
     * Actualizar un movimiento de inventario existente
     *
     * @param  UpDateRequest  $request  Datos actualizados del movimiento de inventario validados
     * @param  string  $id  Identificador del movimiento de inventario a actualizar
     * @return JsonResponse Datos del movimiento de inventario actualizado o error 400/404 si falla
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UpDateRequest $request, string $id): JsonResponse
    {
        $data = InvMoveEntity::fromArray($request->validated());
        $data->id = $id;
        $updated = $this->useCase->update($data);
        if ($updated) {
            $invMove = $this->useCase->find($id);

            return response()->json($invMove);
        }

        return response()->json(['message' => 'Failed to update Inventory Movement'], 400);
    }

    /**
     * Eliminar un movimiento de inventario por ID
     *
     * @param  string  $id  Identificador del movimiento de inventario a eliminar
     * @return JsonResponse Mensaje de éxito o error 404 si no existe
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->useCase->delete($id);
        if ($deleted) {
            return response()->json(['message' => 'Inventory Movement deleted successfully']);
        }

        return response()->json(['message' => 'Inventory Movement not found'], 404);
    }
}
