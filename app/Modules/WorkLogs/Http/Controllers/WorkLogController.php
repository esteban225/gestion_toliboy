<?php

namespace App\Modules\WorkLogs\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\WorkLogs\Application\DTOs\WorkLogDTO;
use App\Modules\WorkLogs\Application\UseCases\PaginateWorkLogUseCase;
use App\Modules\WorkLogs\Application\UseCases\RegisterWorkLogUseCase;
use App\Modules\WorkLogs\Application\UseCases\WorkLogUseCase;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Http\Requests\WorkLogRegisterRequest;
use App\Modules\WorkLogs\Http\Requests\WorkLogUpDateRequest;
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
     */
    public function index(Request $request): JsonResponse
    {

        try {
            $filters = $request->except(['page', 'per_page']);
            $perPage = $request->input('per_page', 15);

            $paginator = $this->paginateWorkLogUseCase->execute($filters, $perPage);

            return response()->json([
                'status' => true,
                'data' => $paginator->items(),
                'meta' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving work logs', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Crear work log
     *
     * Registra un nuevo work log manualmente.
     */
    public function store(WorkLogRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $workLogEntity = WorkLogEntity::fromArray($data);

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
    }

    /**
     * Mostrar work log
     *
     * Obtiene los detalles de un work log por su ID.
     */
    public function show(int $id): JsonResponse
    {
        $workLog = $this->workLogUseCase->getWorkLogById($id);
        $workLogArray = array_map(fn ($workLog) => $workLog->toArray(), [$workLog]);
        if ($workLog) {
            return response()->json($workLogArray);
        }

        return response()->json(['message' => 'Work log not found'], 404);
    }

    /**
     * Actualizar work log
     *
     * Actualiza un work log existente. start_time y end_time deben mantenerse en formato HH:MM si se envían.
     */
    public function update(WorkLogUpDateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $existingWorkLog = $this->workLogUseCase->getWorkLogById($id);

        if (! $existingWorkLog) {
            return response()->json(['message' => 'Work log not found'], 404);
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

        return response()->json($updatedWorkLog);
    }

    /**
     * Eliminar work log
     *
     * Borra un work log por su ID.
     */
    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->workLogUseCase->deleteWorkLog($id);
        if ($deleted) {
            return response()->json(['message' => 'Work log deleted']);
        }

        return response()->json(['message' => 'Work log not found'], 404);
    }

    /**
     * Listar work logs de un usuario
     *
     * Devuelve los work logs del usuario indicado.
     */
    public function showUserWorkLogs(int $userId): JsonResponse
    {
        $workLogs = $this->workLogUseCase->getWorkLogsByUserId($userId);

        $workLogsUser = array_map(fn ($workLog) => $workLog->toArray(), $workLogs);

        return response()->json($workLogsUser);
    }

    /**
     * Registra automáticamente la hora de entrada o salida del trabajador.
     */
    public function registerWorkLog(int $id): JsonResponse
    {
        $workLog = $this->registerWorkLogUseCase->execute($id);

        return response()->json([
            'status' => true,
            'message' => 'Registro actualizado correctamente.',
            'data' => $workLog->toArray(),
        ]);
    }
}
