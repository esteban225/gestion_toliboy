<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Forms\Application\UseCases\ManageFormFieldUseCase;
use App\Modules\Forms\Http\Requests\FormFieldFiltersRequest;
use App\Modules\Forms\Http\Requests\FormFieldRegisterRequest;
use App\Modules\Forms\Http\Requests\FormFieldUpDateRequest;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de campos de formulario.
 * Maneja las operaciones CRUD de los campos pertenecientes a un formulario.
 */
class FormFieldController extends Controller
{
    private ManageFormFieldUseCase $manageFormFieldUseCase;

    public function __construct(ManageFormFieldUseCase $manageFormFieldUseCase)
    {
        $this->manageFormFieldUseCase = $manageFormFieldUseCase;
    }

    /**
     * Lista los campos del formulario especificado.
     */
    public function index(FormFieldFiltersRequest $request, int $formId): JsonResponse
    {
        try {
            $filters = array_merge(
                $request->except(['page', 'per_page']),
                ['form_id' => $formId]
            );
            $perPage = $request->input('per_page', 15);

            $paginator = $this->manageFormFieldUseCase->listFormFields($filters, $perPage);

            if (! $paginator || $paginator->isEmpty()) {
                return response()->json(['message' => 'No se encontraron campos para este formulario'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Campos del formulario recuperados con éxito',
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recuperar los campos', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra un campo específico del formulario.
     */
    public function show(int $formId, int $fieldId): JsonResponse
    {
        try {
            $field = $this->manageFormFieldUseCase->findFormField($fieldId);

            if (! $field || $field->getFormId() !== $formId) {
                return response()->json(['message' => 'Campo no encontrado para este formulario'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Campo recuperado con éxito',
                'data' => $field->toArray(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al recuperar el campo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crea un nuevo campo en el formulario especificado.
     */
    public function store(FormFieldRegisterRequest $request, int $formId): JsonResponse
    {
        try {
            $data = array_merge($request->validated(), ['form_id' => $formId]);

            $created = $this->manageFormFieldUseCase->createFormField($data);

            if (! $created) {
                return response()->json(['message' => 'Error al crear el campo'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Campo creado con éxito',
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el campo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualiza un campo existente del formulario.
     */
    public function update(FormFieldUpDateRequest $request, int $formId, int $fieldId): JsonResponse
    {
        try {
            $data = array_merge($request->all(), [
                'id' => $fieldId,
                'form_id' => $formId,
            ]);

            $updated = $this->manageFormFieldUseCase->updateFormField($data);

            if (! $updated) {
                return response()->json(['message' => 'Error al actualizar el campo'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Campo actualizado con éxito',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el campo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Elimina un campo específico del formulario.
     */
    public function destroy(int $formId, int $fieldId): JsonResponse
    {
        try {
            $deleted = $this->manageFormFieldUseCase->deleteFormField($fieldId);

            if (! $deleted) {
                return response()->json(['message' => 'Error al eliminar el campo'], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Campo eliminado con éxito',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el campo', 'error' => $e->getMessage()], 500);
        }
    }
}
