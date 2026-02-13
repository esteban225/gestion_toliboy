<?php

namespace App\Modules\InventoryMovements\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\InventoryMovements\Application\UseCases\InvMoveUseCase;
use App\Modules\InventoryMovements\Domain\Entities\InvMoveEntity;
use App\Modules\InventoryMovements\Http\Requests\FilterInvMovementRequest;
use App\Modules\InventoryMovements\Http\Requests\RegisterInvMovementRequest;
use App\Modules\InventoryMovements\Http\Requests\UpdateInvMovementRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

/**
 * @class InvMoveController
 *
 * Controlador HTTP para la gestión de movimientos de inventario.
 * Implementa operaciones CRUD usando el caso de uso InvMoveUseCase.
 */
#[Group(name: ' Módulo de Inventario: movimientos de inventario', weight: 8)]
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
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  FilterInvMovementRequest  $request  Filtros opcionales enviados en la petición
     * @return JsonResponse Lista de movimientos de inventario
     */
    public function index(FilterInvMovementRequest $request): JsonResponse
    {
        try {
            $filters = $request->except(['page', 'per_page']);
            $perPage = $request->input('per_page', 15);

            $paginator = $this->useCase->list($filters, $perPage);
            if (! $paginator) {
                return response()->json(['message' => 'No se encontraron movimientos de inventario'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Movimientos de inventario recuperados con éxito',
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recuperar los movimientos de inventario', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar un movimiento de inventario específico por ID
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  Identificador del movimiento de inventario
     * @return JsonResponse Datos del movimiento de inventario o error 404 si no existe
     */
    public function show(int $id): JsonResponse
    {
        try {
            $invMove = $this->useCase->find($id);
            if (! $invMove) {
                return response()->json(['message' => 'Movimiento de inventario no encontrado'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Movimiento de inventario recuperado con éxito',
                'data' => $invMove->toArray(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recuperar el movimiento de inventario', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Registrar un nuevo movimiento de inventario
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  RegisterInvMovementRequest  $request  Datos del nuevo movimiento de inventario validados
     * @return JsonResponse Datos del movimiento de inventario creado o error 400 si falla
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterInvMovementRequest $request): JsonResponse
    {
        try {
            $data = InvMoveEntity::fromArray($request->validated());
            $invMove = $this->useCase->create($data);
            // Si es movimiento de entrada, aumentar stock (por compatibilidad, aunque ya se hace en Service)
            if ($invMove && $data->isInbound()) {
                $this->useCase->increaseStock($data->getRawMaterialId(), $data->getQuantity());
            }
            if ($invMove) {
                return response()->json([
                    'success' => true,
                    'message' => 'Movimiento de inventario creado con éxito',
                ], 201);
            }

            return response()->json([
                'success' => false,
                'message' => 'Falla al crear el movimiento de inventario',
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el movimiento de inventario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Actualizar un movimiento de inventario existente
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  UpdateInvMovementRequest  $request  Datos actualizados del movimiento de inventario validados
     * @param  int  $id  Identificador del movimiento de inventario a actualizar
     * @return JsonResponse Datos del movimiento de inventario actualizado o error 400/404 si falla
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(UpdateInvMovementRequest $request, int $id): JsonResponse
    {
        try {
            $data = InvMoveEntity::fromArray($request->validated());
            $data->setId($id);
            $updated = $this->useCase->update($data);

            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => "Movimiento de inventario con id {$id} no encontrado o no actualizado",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Movimiento de inventario actualizado con éxito',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error updating Inventory Movement', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar un movimiento de inventario por ID
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  Identificador del movimiento de inventario a eliminar
     * @return JsonResponse Mensaje de éxito o error 404 si no existe
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->useCase->delete($id);
            if (! $deleted) {
                return response()->json(['message' => 'Movimiento de inventario no encontrado'], 404);
            }

            return response()->json(['message' => 'Movimiento de inventario eliminado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el movimiento de inventario', 'error' => $e->getMessage()], 500);
        }
    }
}
