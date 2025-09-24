<?php

namespace App\Modules\RawMaterials\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\RawMaterials\Application\UseCases\RawMaterialUseCase;
use App\Modules\RawMaterials\Http\Requests\RegisterRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @class RawMaterialsController
 *
 * Controlador HTTP para la gestión de materias primas.
 * Implementa operaciones CRUD usando el caso de uso RawMaterialUseCase.
 */
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
     * @param  Request  $request  Filtros opcionales enviados en la petición
     * @return JsonResponse Lista de materias primas
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->all();
        $rawMaterials = $this->useCase->list($filters);

        return response()->json($rawMaterials);
    }

    /**
     * Mostrar una materia prima específica por ID
     *
     * @param  string  $id  Identificador de la materia prima
     * @return JsonResponse Datos de la materia prima o error 404 si no existe
     */
    public function show(string $id): JsonResponse
    {
        $rawMaterial = $this->useCase->find($id);
        if ($rawMaterial) {
            return response()->json($rawMaterial);
        }

        return response()->json(['message' => 'Raw Material not found'], 404);
    }

    /**
     * Crear una nueva materia prima
     *
     * @param  RegisterRequest  $request  Datos validados para la creación
     * @return JsonResponse Datos de la materia prima creada
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $rawMaterial = $this->useCase->create($data);

        return response()->json($rawMaterial, 201);
    }

    /**
     * Actualizar una materia prima existente
     *
     * @param  RegisterRequest  $request  Datos validados para actualizar
     * @param  string  $id  Identificador de la materia prima a actualizar
     * @return JsonResponse Mensaje de éxito o error 404 si no existe
     */
    public function update(RegisterRequest $request, string $id): JsonResponse
    {
        $data = array_merge(['id' => $id], $request->all());
        $updated = $this->useCase->update($data);
        if ($updated) {
            return response()->json(['message' => 'Raw Material updated successfully']);
        }

        return response()->json(['message' => 'Raw Material not found or not updated'], 404);
    }

    /**
     * Eliminar una materia prima
     *
     * @param  string  $id  Identificador de la materia prima
     * @return JsonResponse Mensaje de éxito o error 404 si no existe
     */
    public function destroy(string $id): JsonResponse
    {
        $deleted = $this->useCase->delete($id);
        if ($deleted) {
            return response()->json(['message' => 'Raw Material deleted successfully']);
        }

        return response()->json(['message' => 'Raw Material not found or not deleted'], 404);
    }
}
