<?php

namespace App\Modules\Forms\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Forms\Application\UseCases\ManageFormUseCase;
use App\Modules\Forms\Http\Requests\FormFiltersRequest;
use App\Modules\Forms\Http\Requests\FormRegisterRequest;
use App\Modules\Forms\Http\Requests\FormUpDateRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;

/**
 * Controlador de Formularios.
 *
 * Gestiona todas las operaciones relacionadas con los formularios: listar, ver, crear, actualizar y eliminar.
 */
#[Group(name: 'Módulo de Formularios: Gestión de formularios', weight: 9)]
class FormsController extends Controller
{
    /**
     * Constructor que inyecta el caso de uso principal.
     */
    public function __construct(private ManageFormUseCase $useCase)
    {
        $this->useCase = $useCase;
    }

    /**
     * Listar formularios.
     *
     * Este endpoint proporciona un listado completo de los formularios disponibles en el sistema.
     *
     * Características principales:
     *
     * - Paginación integrada con tamaño personalizable por página
     * - Filtros dinámicos por:
     *   * Nombre del formulario
     *   * Estado (activo/inactivo)
     *   * Fecha de creación
     *   * Versión
     *   * Categoría
     * - Ordenamiento configurable
     * - Inclusión opcional de metadatos y estadísticas de uso
     * - Información resumida de campos asociados
     *
     * @endpoint GET /api/forms
     *
     * @param  FormFiltersRequest  $request  Contiene los parámetros de filtrado y paginación
     * @return JsonResponse Lista paginada de formularios con metadata
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
                'message' => 'Formularios recuperados con éxito',
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
     * Mostrar un formulario.
     *
     * Este endpoint recupera la información detallada de un formulario específico.
     *
     * La respuesta incluye:
     *
     * - Información básica del formulario (nombre, descripción, versión)
     * - Lista completa de campos asociados y su configuración
     * - Reglas de validación generales del formulario
     * - Estadísticas de uso y completitud
     * - Historial de versiones si está disponible
     * - Configuración de flujo de trabajo y permisos
     * - Metadata adicional como fechas de creación/modificación
     *
     * @endpoint GET /api/forms/{id}
     *
     * @param  int  $id  Identificador único del formulario
     * @return JsonResponse Datos completos del formulario y sus relaciones
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
     * Crear formulario.
     *
     * Este endpoint permite crear un nuevo formulario en el sistema.
     *
     * El proceso de creación incluye:
     *
     * - Validación de datos básicos (nombre, descripción, tipo)
     * - Generación automática de código único
     * - Configuración inicial de versión
     * - Establecimiento de permisos y visibilidad
     * - Creación de estructura base para campos
     * - Configuración de flujo de trabajo si se especifica
     * - Asignación de categorías y etiquetas
     *
     * @endpoint POST /api/forms
     *
     * @param  FormRegisterRequest  $request  Datos completos del nuevo formulario
     * @return JsonResponse Confirmación de creación y datos del formulario
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
     * Actualizar formulario.
     *
     * Este endpoint permite modificar un formulario existente.
     *
     * Capacidades de actualización:
     *
     * - Modificación de datos básicos (nombre, descripción)
     * - Actualización de versión con control de cambios
     * - Gestión de estado (activación/desactivación)
     * - Modificación de configuraciones de validación
     * - Ajuste de permisos y visibilidad
     * - Actualización de categorías y etiquetas
     * - Mantenimiento de la integridad con respuestas existentes
     *
     * @endpoint PUT /api/forms/{id}
     *
     * @param  FormUpDateRequest  $request  Datos actualizados del formulario
     * @param  int  $id  Identificador del formulario a actualizar
     * @return JsonResponse Confirmación de actualización y datos modificados
     */
    public function update(FormUpDateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->all();
            $data['id'] = $id;
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
     * Eliminar formulario.
     *
     * Este endpoint permite eliminar permanentemente un formulario del sistema.
     *
     * El proceso de eliminación incluye:
     *
     * - Verificación de seguridad y permisos
     * - Validación de dependencias y relaciones
     * - Eliminación en cascada de:
     *   * Todos los campos asociados
     *   * Respuestas almacenadas
     *   * Archivos adjuntos
     *   * Configuraciones personalizadas
     *   * Historial de versiones
     * - Registro de la acción para auditoría
     *
     * NOTA: Esta operación es irreversible y eliminará todos los datos relacionados.
     *
     * @endpoint DELETE /api/forms/{id}
     *
     * @param  int  $id  Identificador del formulario a eliminar
     * @return JsonResponse Confirmación de la eliminación
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
