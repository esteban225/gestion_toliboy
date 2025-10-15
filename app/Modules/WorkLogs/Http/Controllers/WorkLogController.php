<?php

namespace App\Modules\WorkLogs\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\WorkLogs\Application\DTOs\WorkLogDTO;
use App\Modules\WorkLogs\Application\UseCases\PaginateWorkLogUseCase;
use App\Modules\WorkLogs\Application\UseCases\RegisterWorkLogUseCase;
use App\Modules\WorkLogs\Application\UseCases\WorkLogUseCase;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Http\Requests\WorkLogFilterRequest;
use App\Modules\WorkLogs\Http\Requests\WorkLogRegisterRequest;
use App\Modules\WorkLogs\Http\Requests\WorkLogUpDateRequest;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group WorkLogs
 *
 * @description Endpoints para gestionar los registros de jornada laboral (work logs).
 *
 * Notas:
 * - El formato de hora para start_time y end_time debe ser HH:MM (24 horas), por ejemplo "08:30" o "17:45".
 * - Todas las respuestas siguen el esquema JSON estándar: { "success": boolean, "data": mixed|null, "message": string|null } o equivalente.
 */

/**
 * Short description:
 * Controlador responsable de exponer endpoints para gestionar registros de
 * jornada laboral (work logs): listar, crear, consultar, actualizar,
 * eliminar y registrar entrada/salida automática.
 *
 * Nota: La documentación detallada de cada endpoint se mantiene en los
 * comentarios existentes; aquí solo añadimos una descripción general corta.
 */
#[Group(name: 'Módulo de Usuarios: Horas de trabajo', weight: 4)]
class WorkLogController extends Controller
{
    private WorkLogUseCase $workLogUseCase;

    private PaginateWorkLogUseCase $paginateWorkLogUseCase;

    private RegisterWorkLogUseCase $registerWorkLogUseCase;

    public function __construct(
        WorkLogUseCase $workLogUseCase,
        PaginateWorkLogUseCase $paginateWorkLogUseCase,
        RegisterWorkLogUseCase $registerWorkLogUseCase
    ) {
        $this->workLogUseCase = $workLogUseCase;
        $this->paginateWorkLogUseCase = $paginateWorkLogUseCase;
        $this->registerWorkLogUseCase = $registerWorkLogUseCase;
    }

    /**
     * Listar work logs
     *
     * Devuelve todos los work logs registrados.
     * Soporta filtros opcionales por user_id y date.
     * Retorna meta información de paginación en la respuesta.
     * Los roles que pueden acceder a esta acción son:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function index(WorkLogFilterRequest $request): JsonResponse
    {

        try {
            $filters = $request->except(['page', 'per_page']);
            $perPage = $request->input('per_page', 15);

            $paginator = $this->paginateWorkLogUseCase->execute($filters, $perPage);
            if ($paginator->isEmpty()) {
                return response()->json(['message' => 'No se encontraron registros de las horas de trabajo'], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Registros de las horas de trabajo recuperados con éxito',
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los registros de las horas de trabajo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear work log
     *
     * Registra un nuevo work log manualmente.
     * start_time y end_time deben estar en formato HH:MM (24 horas).
     * total_hours y overtime_hours son opcionales; si no se proporcionan, se calcularán automáticamente.
     * batch_id, task_description y notes son campos opcionales para información adicional.
     * Los campos user_id, date, start_time y end_time son obligatorios.
     * total_hours y overtime_hours no se envían, se calcularán en base a start_time y end_time.
     *
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function store(WorkLogRegisterRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $workLogEntity = WorkLogEntity::fromArray($data);
            if (! $workLogEntity) {
                return response()->json(['message' => 'Datos inválidos para crear el registro de la hora de trabajo'], 400);
            }

            // Convert WorkLogEntity to WorkLogDTO
            $workLogDTO = new WorkLogDTO(
                $workLogEntity->getUserId(),
                $workLogEntity->getDate(),
                $workLogEntity->getStartTime(),
                $workLogEntity->getEndTime(),
                $workLogEntity->getTotalHours() ?? null,
                $workLogEntity->getOvertimeHours() ?? null,
                $workLogEntity->getBatchId() ?? null,
                $workLogEntity->getTaskDescription() ?? null,
                $workLogEntity->getNotes() ?? null
            );

            $createdWorkLog = $this->workLogUseCase->createWorkLog($workLogDTO);

            return response()->json($createdWorkLog, 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al crear el registro de la hora de trabajo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Mostrar work log
     *
     * Obtiene los detalles de un work log por su ID.
     * Si no se encuentra, retorna un error 404.
     * Los roles que pueden acceder a esta acción son:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function show(int $id): JsonResponse
    {
        try {
            $workLog = $this->workLogUseCase->getWorkLogById($id);
            $workLogArray = array_map(fn ($workLog) => $workLog->toArray(), [$workLog]);
            if ($workLog) {
                return response()->json([
                    'status' => true,
                    'message' => 'Registro de la hora de trabajo recuperado con éxito',
                    'data' => $workLogArray,
                ], 200);
            }

            return response()->json(['message' => 'hora de trabajo no encontrada'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener el registro de la hora de trabajo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar work log
     *
     * Actualiza un work log existente. start_time y end_time deben mantenerse en formato HH:MM si se envían.
     * total_hours y overtime_hours son opcionales; si no se proporcionan, se recalcularán automáticamente.
     * batch_id, task_description y notes son campos opcionales para información adicional.
     * Los campos user_id, date, start_time y end_time son obligatorios.
     * total_hours y overtime_hours no se envían, se calcularán en base a start_time y end_time.
     * Si el work log no existe, retorna un error 404.
     *
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function update(WorkLogUpDateRequest $request, int $id): JsonResponse
    {
        try {
            $data = $request->validated();
            $existingWorkLog = $this->workLogUseCase->getWorkLogById($id);

            if (! $existingWorkLog) {
                return response()->json(['message' => 'No se encontró el registro de la hora de trabajo'], 404);
            }

            // Actualizar solo los campos proporcionados
            foreach ($data as $key => $value) {
                if (property_exists($existingWorkLog, $key)) {
                    $existingWorkLog->$key = $value;
                }
            }

            // Convert updated WorkLogEntity to WorkLogDTO
            $workLogDTO = new WorkLogDTO(
                $existingWorkLog->getUserId(),
                $existingWorkLog->getDate(),
                $existingWorkLog->getStartTime(),
                $existingWorkLog->getEndTime(),
                $existingWorkLog->getTotalHours() ?? null,
                $existingWorkLog->getOvertimeHours() ?? null,
                $existingWorkLog->getBatchId() ?? null,
                $existingWorkLog->getTaskDescription() ?? null,
                $existingWorkLog->getNotes() ?? null
            );

            $updatedWorkLog = $this->workLogUseCase->updateWorkLog($workLogDTO);

            return response()->json([
                'status' => true,
                'message' => 'Registro de la hora de trabajo actualizado con éxito',
                'data' => $updatedWorkLog,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al actualizar el registro de la hora de trabajo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar work log
     *
     * Borra un work log por su ID.
     *
     * Si no se encuentra, retorna un error 404.
     * Los roles que pueden acceder a esta acción son:
     *
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deleted = $this->workLogUseCase->deleteWorkLog($id);
            if ($deleted) {
                return response()->json(['message' => 'Registro de la hora de trabajo eliminado con éxito'], 200);
            }

            return response()->json(['message' => 'Registro de la hora de trabajo no encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al eliminar el registro de la hora de trabajo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Listar work logs de un usuario
     *
     * Devuelve los work logs del usuario indicado.
     * Si el usuario no tiene work logs, retorna un array vacío.
     * Esté endpoint es público y no requiere roles .
     */
    public function showUserWorkLogs(int $userId): JsonResponse
    {
        try {
            $workLogs = $this->workLogUseCase->getWorkLogsByUserId($userId);
            if (! $workLogs) {
                return response()->json(['message' => 'No se encontraron registros de las horas de trabajo del usuario'], 404);
            }

            $workLogsUser = array_map(fn ($workLog) => $workLog->toArray(), $workLogs);

            return response()->json([
                'status' => true,
                'message' => 'Registros de las horas de trabajo del usuario recuperados con éxito',
                'data' => $workLogsUser,
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al obtener los registros de las horas de trabajo del usuario', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Registra automáticamente la hora de entrada o salida del trabajador.
     *
     * Si el usuario no tiene un registro abierto (sin hora de salida), se crea uno nuevo con la hora de entrada actual.
     * Si ya tiene un registro abierto, se actualiza con la hora de salida actual y se calculan las horas totales y extra.
     * Retorna el registro creado o actualizado.
     *
     * Si el usuario no existe, retorna un error 404.
     *
     * Los roles que pueden acceder a esta acción son:
     * - DEV = Desarrollador
     * - GG = Gerente General
     * - INGPL = Ingeniero de Planta
     * - INGPR = Ingeniero de Producción
     */
    public function registerWorkLog(Request $request, int $id): JsonResponse
    {
        $batchId = $request->query('batch_id'); // ejemplo: ?batch_id=1
        $taskDescription = $request->query('task_description'); // ejemplo: ?task_description=Desarrollo
        $notes = $request->query('notes'); // ejemplo: ?notes=Revisar

        // Si quieres validar los parámetros manualmente:
        $validated = $request->validate([
            'batch_id' => 'sometimes|integer|exists:batches,id',
            'task_description' => 'sometimes|string|max:255',
            'notes' => 'sometimes|string|max:500',
        ]);
        try {
            // Ejecuta el caso de uso para registrar la hora de entrada o salida
            $workLog = $this->registerWorkLogUseCase->execute($validated, $id);
            if (! $workLog) {
                return response()->json(['message' => 'No se encontró el usuario para registrar la hora de trabajo'], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Registro actualizado correctamente.',
                'data' => $workLog->toArray(),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al registrar el registro de la hora de trabajo', 'error' => $e->getMessage()], 500);
        }
    }
}
