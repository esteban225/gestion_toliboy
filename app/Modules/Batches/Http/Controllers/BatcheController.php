<?php

namespace App\Modules\Batches\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Batches\Application\UseCases\BatcheUseCase;
use App\Modules\Batches\Http\Requests\BatchRegisterRequest;
use App\Modules\Batches\Http\Requests\BatchUpdateRequest;
use App\Modules\Batches\Http\Requests\FilterBatchRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

/**
 * @group Batches
 *
 * @description Endpoints para la gestión de lotes (batches).
 *
 * Controlador responsable de listar, mostrar, crear, actualizar y eliminar lotes.
 * Las respuestas están formateadas para Scramble/OpenAPI.
 */
#[Group(name: 'Modulo de Inventario: Lotes', weight: 7)]
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
     * Soporta paginación y varios criterios de filtrado.
     * Filtros soportados: code, product_id, status, per_page, page.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function index(FilterBatchRequest $request): JsonResponse
    {
        try {
            $filters = $request->except(['page', 'per_page']);
            $perPage = $request->input('per_page', 15);

            $paginator = $this->useCase->list($filters, $perPage);
            if (! $paginator) {
                return response()->json(['message' => 'No se encontraron lotes'], 404);
            }

            $data = $paginator->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Lotes recuperados con éxito',
                'data' => $data['data'],
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al recuperar los lotes', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar lote
     *
     * Obtiene los detalles de un lote por su ID.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function show(int $id): JsonResponse
    {
        try {
            $batche = $this->useCase->find($id);
            if (! $batche) {
                return response()->json(['success' => false, 'message' => 'Lote no encontrado'], 404);
            }
            $data = $batche->toArray();

            return response()->json([
                'success' => true,
                'message' => 'Lote recuperado con éxito',
                'data' => $data,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al recuperar el lote', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear lote
     *
     * Registra un nuevo lote en el sistema.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function store(BatchRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $batche = $this->useCase->create($data);

            if (! $batche) {
                return response()->json(['success' => false, 'message' => 'Lote no creado'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Lote creado con éxito',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al crear el lote', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar lote
     *
     * Actualiza los datos de un lote existente.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function update(BatchUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $data = array_merge(['id' => $id], $request->all());
            $updated = $this->useCase->update($data);

            if (! $updated) {
                return response()->json(['success' => false, 'message' => 'Lote no encontrado o no actualizado'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Lote actualizado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al actualizar el lote', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar lote
     *
     * Elimina un lote por su ID.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->useCase->delete($id);

            if (! $deleted) {
                return response()->json(['success' => false, 'message' => 'Lote no encontrado o no eliminado'], 404);
            }

            return response()->json(['success' => true, 'message' => 'Lote eliminado con éxito'], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el lote', 'error' => $e->getMessage()], 500);
        }
    }
}
