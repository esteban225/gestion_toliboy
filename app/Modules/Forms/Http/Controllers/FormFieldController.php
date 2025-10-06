<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Forms\Application\UseCases\ManageFormFieldUseCase;
use App\Modules\Forms\Http\Requests\FormFieldFiltersRequest;
use App\Modules\Forms\Http\Requests\FormFieldRegisterRequest;
use App\Modules\Forms\Http\Requests\FormFieldUpDateRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de Campos de Formulario.
 *
 * Gestiona las operaciones CRUD de los campos asociados a un formulario.
 */
#[Group(name: 'Módulo de Formularios: Gestión de campos de formulario', weight: 10)]
class FormFieldController extends Controller
{
    private ManageFormFieldUseCase $manageFormFieldUseCase;

    public function __construct(ManageFormFieldUseCase $manageFormFieldUseCase)
    {
        $this->manageFormFieldUseCase = $manageFormFieldUseCase;
    }

    /**
     * Listar campos de formulario.
     *
     * Este endpoint proporciona una lista paginada de todos los campos asociados a un formulario específico.
     *
     * Características principales:
     *
     * - Soporta filtrado por múltiples criterios (tipo de campo, estado, etc.)
     * - Paginación configurable con número de elementos por página personalizable
     * - Devuelve metadata de paginación para navegación
     * - Ordena los campos según su configuración en el formulario
     * - Incluye información sobre el estado activo/inactivo de cada campo
     *
     * @endpoint GET /api/forms/{formId}/fields
     *
     * @param  FormFieldFiltersRequest  $request  Contiene los filtros y parámetros de paginación
     * @param  int  $formId  Identificador único del formulario del que se quieren obtener los campos
     * @return JsonResponse Lista paginada de campos con metadata de paginación
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
     * Mostrar un campo del formulario.
     *
     * Este endpoint recupera la información detallada de un campo específico del formulario.
     *
     * Incluye:
     *
     * - Propiedades básicas del campo (nombre, tipo, etiqueta)
     * - Reglas de validación asociadas
     * - Opciones disponibles para campos de selección
     * - Estado actual del campo
     * - Configuración de visualización
     * - Verifica que el campo pertenezca al formulario especificado
     *
     * @endpoint GET /api/forms/{formId}/fields/{fieldId}
     *
     * @param  int  $formId  Identificador del formulario al que pertenece el campo
     * @param  int  $fieldId  Identificador único del campo a recuperar
     * @return JsonResponse Detalles completos del campo solicitado
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
     * Crear un nuevo campo.
     *
     * Este endpoint permite añadir un nuevo campo a un formulario existente.
     *
     * El proceso incluye:
     *
     * - Validación del tipo de campo y sus propiedades
     * - Asignación automática al formulario especificado
     * - Configuración de reglas de validación
     * - Establecimiento del orden del campo en el formulario
     * - Definición de opciones para campos de selección
     * - Configuración de la visualización y comportamiento del campo
     *
     * @endpoint POST /api/forms/{formId}/fields
     *
     * @param  FormFieldRegisterRequest  $request  Datos del nuevo campo a crear
     * @param  int  $formId  Identificador del formulario donde se agregará el campo
     * @return JsonResponse Confirmación de la creación del campo
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
     * Actualizar un campo existente.
     *
     * Este endpoint permite modificar las propiedades de un campo existente.
     *
     * Características de la actualización:
     *
     * - Permite modificar etiquetas y descripciones
     * - Actualiza reglas de validación
     * - Modifica opciones de campos de selección
     * - Ajusta el orden del campo en el formulario
     * - Cambia el estado activo/inactivo
     * - Mantiene la integridad con respuestas existentes
     * - Verifica que el campo pertenezca al formulario especificado
     *
     * @endpoint PUT /api/forms/{formId}/fields/{fieldId}
     *
     * @param  FormFieldUpDateRequest  $request  Datos actualizados del campo
     * @param  int  $formId  Identificador del formulario al que pertenece el campo
     * @param  int  $fieldId  Identificador único del campo a actualizar
     * @return JsonResponse Confirmación de la actualización del campo
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
     * Eliminar un campo.
     *
     * Este endpoint permite eliminar permanentemente un campo de un formulario.
     *
     * El proceso de eliminación:
     *
     * - Verifica que el campo pertenezca al formulario especificado
     * - Elimina todas las respuestas asociadas al campo
     * - Reordena los campos restantes si es necesario
     * - Actualiza la estructura del formulario
     * - Mantiene un registro de la eliminación para auditoría
     *
     * Nota: Esta operación es irreversible y afectará a todas las respuestas existentes.
     *
     * @endpoint DELETE /api/forms/{formId}/fields/{fieldId}
     *
     * @param  int  $formId  Identificador del formulario del que se eliminará el campo
     * @param  int  $fieldId  Identificador único del campo a eliminar
     * @return JsonResponse Confirmación de la eliminación del campo
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
