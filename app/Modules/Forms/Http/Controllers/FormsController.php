<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Forms\Application\UseCases\ManageFormUseCase;
use App\Modules\Forms\Http\Requests\FormFiltersRequest;
use App\Modules\Forms\Http\Requests\FormRegisterRequest;
use App\Modules\Forms\Http\Requests\FormUpDateRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de formularios.
 *
 * Provee los endpoints HTTP para listar, mostrar, crear, actualizar y eliminar formularios.
 */
class FormsController extends Controller
{
    /**
     * Constructor que inyecta el caso de uso para gestionar formularios.
     *
     * @param  ManageFormUseCase  $useCase  Caso de uso de formularios.
     */
    public function __construct(private ManageFormUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Lista formularios con filtros y paginación.
     *
     * @param  FormFiltersRequest  $request  Solicitud con filtros y parámetros de paginación.
     * @return JsonResponse Respuesta JSON con los datos paginados o error.
     */
    public function index(FormFiltersRequest $request): JsonResponse
    {
        try {
            $filters = $request->except(['page', 'per_page']);
            $perpage = $request->input('per_page', 15);

            $paginator = $this->useCase->list($filters, $perpage);
            if (! $paginator) {
                return response()->json(['message' => 'No se encontraron formularios'], 404);
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
            return response()->json(['message' => 'Error al recuperar formularios', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra un formulario específico.
     *
     * @param  int  $id  Identificador del formulario.
     * @return JsonResponse Respuesta JSON con el formulario o mensaje de error.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $form = $this->useCase->get($id);
            if (! $form) {
                return response()->json(['message' => 'Formulario no encontrado'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Formulario recuperado con éxito',
                'data' => $form->toArray(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recuperar el formulario', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Almacena un nuevo formulario.
     *
     * @param  FormRegisterRequest  $request  Solicitud validada con datos del formulario.
     * @return JsonResponse Respuesta JSON confirmando la creación o error.
     */
    public function store(FormRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $form = $this->useCase->create($data);

            if (! $form) {
                return response()->json(['message' => 'Error al crear el formulario'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Formulario creado con éxito',

            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el formulario', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza un formulario existente.
     *
     * @param  FormUpDateRequest  $request  Solicitud con datos a actualizar.
     * @param  int  $id  Identificador del formulario.
     * @return JsonResponse Respuesta JSON confirmando la actualización o error.
     */
    public function update(FormUpDateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->all();
            $data['id'] = $id; // Asegurarse de que el ID esté incluido en los datos
            $form = $this->useCase->update($data);

            if (! $form) {
                return response()->json(['message' => 'Error al actualizar el formulario'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Formulario actualizado con éxito',

            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el formulario', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un formulario por su identificador.
     *
     * @param  int  $id  Identificador del formulario.
     * @return JsonResponse Respuesta JSON confirmando la eliminación o error.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->useCase->delete($id);
            if (! $deleted) {
                return response()->json(['message' => 'Error al eliminar el formulario'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Formulario eliminado con éxito',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el formulario', 'error' => $e->getMessage()], 500);
        }
    }
}
