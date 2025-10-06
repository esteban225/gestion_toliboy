<?php

namespace App\Modules\RawMaterials\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\RawMaterials\Application\UseCases\RawMaterialUseCase;
use App\Modules\RawMaterials\Domain\Entities\RawMaterialEntity;
use App\Modules\RawMaterials\Http\Requests\FilterRawMaterialRequest;
use App\Modules\RawMaterials\Http\Requests\RawMaterialRegisterRequest;
use App\Modules\RawMaterials\Http\Requests\RawMaterialUpdateRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @class RawMaterialsController
 *
 * Controlador HTTP para la gestión de materias primas.
 * Implementa operaciones CRUD usando el caso de uso RawMaterialUseCase.
 */
#[Group(name: 'Módulo de Inventario: Materia prima', weight: 5)]
class RawMaterialsController extends Controller
{
    private RawMaterialUseCase $useCase;

    /**
     * Constructor del controlador
     *
     * @param  RawMaterialUseCase  $useCase  Caso de uso para manejar materias primas
     */
    public function __construct(RawMaterialUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar todas las materias primas
     *
     * La lista de materias primas puede ser filtrada usando parámetros
     * opcionales en la request, como 'name' o 'is_active'.
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  Request  $request  Filtros opcionales enviados en la petición
     * @return JsonResponse Lista de materias primas
     */
    public function index(FilterRawMaterialRequest $request): JsonResponse
    {
        try {
            $filters = $request->except(['page', 'per_page']);
            $perPage = $request->input('per_page', 15);

            $paginator = $this->useCase->list($filters, $perPage);
            if (! $paginator) {
                return response()->json(['message' => 'No se encontraron materias primas'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Materias primas recuperadas con éxito',
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recuperar las materias primas', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar una materia prima específica por ID
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  Identificador de la materia prima
     * @return JsonResponse Datos de la materia prima o error 404 si no existe
     */
    public function show(int $id): JsonResponse
    {
        try {
            $rawMaterial = $this->useCase->find($id);

            if (! $rawMaterial) {
                return response()->json(['message' => 'Materia prima no encontrada'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Materia Prima obtenida con éxito',
                'data' => $rawMaterial->toArray(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recuperar la materia prima', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear una nueva materia prima
     *
     *  Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  RegisterRequest  $request  Datos validados para la creación
     * @return JsonResponse Datos de la materia prima creada
     */
    public function store(RawMaterialRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $request = RawMaterialEntity::fromArray($data);
            $rawMaterial = $this->useCase->create($request);

            if (! $rawMaterial) {
                return response()->json(['message' => 'Error al crear la materia prima'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Materia Prima creada con éxito',
            ], 201);
        } catch (\TypeError $e) {
            return response()->json(['message' => 'Datos inválidos proporcionados', 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Actualizar una materia prima existente
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  RegisterRequest  $request  Datos validados para actualizar
     * @param  int  $id  Identificador de la materia prima a actualizar
     * @return JsonResponse Mensaje de éxito o error 404 si no existe
     */
    public function update(RawMaterialUpdateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $data['id'] = $id; // Asegurar que el ID esté en los datos
            $updated = $this->useCase->update(new RawMaterialEntity(...$data));
            if (! $updated) {
                return response()->json([
                    'success' => false,
                    'message' => "Materia Prima con id {$id} no encontrada o no actualizada",
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Materia Prima actualizada con éxito',
            ], 200);
        } catch (\TypeError $e) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos proporcionados',
                'error' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Eliminar una materia prima
     *
     * Esta acción responde bajo estos roles:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     *
     * @param  int  $id  Identificador de la materia prima
     * @return JsonResponse Mensaje de éxito o error 404 si no existe
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->useCase->delete($id);
        if ($deleted) {
            return response()->json(['message' => 'Raw Material deleted successfully']);
        }

        return response()->json(['message' => 'Materia Prima no encontrada o no eliminada'], 404);
    }
}
