<?php

namespace App\Modules\WorkLogs\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Application\UseCases\WorkLogUseCase;
use App\Modules\WorkLogs\Http\Requests\RegisterRequest;
use App\Modules\WorkLogs\Http\Requests\UpDateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group WorkLogs
 * @description Endpoints para gestionar los registros de jornada laboral (work logs).
 *
 * Notas:
 * - El formato de hora para start_time y end_time debe ser HH:MM (24 horas), por ejemplo "08:30" o "17:45".
 * - Todas las respuestas siguen el esquema JSON estándar: { "success": boolean, "data": mixed|null, "message": string|null } o equivalente.
 */
class WorkLogController extends Controller
{
    private WorkLogUseCase $workLogUseCase;

    public function __construct(WorkLogUseCase $workLogUseCase)
    {
        $this->workLogUseCase = $workLogUseCase;
    }

    /**
     * Listar work logs
     *
     * Devuelve todos los work logs o los del usuario indicado.
     *
     * @queryParam user_id int Opcional. Filtra por ID de usuario. Example: 12
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "user_id": 12,
     *       "date": "2025-09-26",
     *       "start_time": "08:00",
     *       "end_time": "17:00",
     *       "total_hours": 8,
     *       "overtime_hours": 0,
     *       "batch_id": 5,
     *       "task_description": "Producción",
     *       "notes": "Observaciones..."
     *     }
     *   ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->query('user_id');
        if ($userId) {
            $workLogs = $this->workLogUseCase->getWorkLogsByUserId((int)$userId);
        } else {
            $workLogs = $this->workLogUseCase->getAllWorkLogs();
        }
        return response()->json($workLogs);
    }

    /**
     * Crear work log
     *
     * Registra un nuevo work log.
     *
     * @bodyParam user_id int required ID del trabajador. Example: 12
     * @bodyParam date date Opcional. Fecha del registro. Example: "2025-09-26"
     * @bodyParam start_time string required Hora de inicio en formato HH:MM (24h). Example: "08:00"
     * @bodyParam end_time string required Hora de fin en formato HH:MM (24h). Example: "17:00"
     * @bodyParam total_hours number Opcional. Total de horas trabajadas. Example: 8
     * @bodyParam overtime_hours number Opcional. Horas extras. Example: 1.5
     * @bodyParam batch_id int Opcional. ID del lote asociado. Example: 5
     * @bodyParam task_description string Opcional. Descripción de la tarea. Example: "Producción"
     * @bodyParam notes string Opcional. Notas adicionales. Example: "Turno fijo"
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 10,
     *     "user_id": 12,
     *     "date": "2025-09-26",
     *     "start_time": "08:00",
     *     "end_time": "17:00",
     *     "total_hours": 8
     *   },
     *   "message": "Work log creado correctamente."
     * }
     * @response 422 {
     *   "success": false,
     *   "message": "Validation error message"
     * }
     */
    public function store(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $workLogEntity = new WorkLogEntity(
            id: null,
            user_id: $data['user_id'],
            date: $data['date'] ?? null,
            start_time: $data['start_time'] ?? null,
            end_time: $data['end_time'] ?? null,
            total_hours: $data['total_hours'] ?? null,
            overtime_hours: $data['overtime_hours'] ?? null,
            batch_id: $data['batch_id'] ?? null,
            task_description: $data['task_description'] ?? null,
            notes: $data['notes'] ?? null,
        );

        $createdWorkLog = $this->workLogUseCase->createWorkLog($workLogEntity);
        return response()->json($createdWorkLog, 201);
    }

    /**
     * Mostrar work log
     *
     * Obtiene los detalles de un work log por su ID.
     *
     * @urlParam id int required ID del work log. Example: 1
     * @response 200 {
     *   "success": true,
     *   "data": { "id": 1, "user_id": 12, "date": "2025-09-26", "start_time": "08:00", "end_time": "17:00" }
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Work log not found"
     * }
     */
    public function show(int $id): JsonResponse
    {
        $workLog = $this->workLogUseCase->getWorkLogById($id);
        if ($workLog) {
            return response()->json($workLog);
        }
        return response()->json(['message' => 'Work log not found'], 404);
    }

    /**
     * Actualizar work log
     *
     * Actualiza un work log existente. start_time y end_time deben mantenerse en formato HH:MM si se envían.
     *
     * @urlParam id int required ID del work log a actualizar. Example: 1
     * @bodyParam date date Opcional. Fecha del registro. Example: "2025-09-26"
     * @bodyParam start_time string Opcional. Hora de inicio en formato HH:MM. Example: "08:00"
     * @bodyParam end_time string Opcional. Hora de fin en formato HH:MM. Example: "17:00"
     * @bodyParam total_hours number Opcional. Total de horas trabajadas. Example: 8
     * @bodyParam overtime_hours number Opcional. Horas extras. Example: 1.5
     * @bodyParam task_description string Opcional. Descripción de la tarea. Example: "Producción"
     *
     * @response 200 {
     *   "success": true,
     *   "data": { "id": 1, "total_hours": 8 },
     *   "message": "Work log actualizado correctamente."
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Work log not found"
     * }
     */
    public function update(UpDateRequest $request, int $id): JsonResponse
    {
        $data = $request->validated();
        $existingWorkLog = $this->workLogUseCase->getWorkLogById($id);

        if (!$existingWorkLog) {
            return response()->json(['message' => 'Work log not found'], 404);
        }

        // Actualizar solo los campos proporcionados
        foreach ($data as $key => $value) {
            if (property_exists($existingWorkLog, $key)) {
                $existingWorkLog->$key = $value;
            }
        }
        $updatedWorkLog = $this->workLogUseCase->updateWorkLog($existingWorkLog);
        return response()->json($updatedWorkLog);
    }

    /**
     * Eliminar work log
     *
     * Borra un work log por su ID.
     *
     * @urlParam id int required ID del work log a eliminar. Example: 1
     * @response 200 {
     *   "success": true,
     *   "message": "Work log deleted"
     * }
     * @response 404 {
     *   "success": false,
     *   "message": "Work log not found"
     * }
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
     *
     * @urlParam userId int required ID del usuario. Example: 12
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     { "id": 1, "user_id": 12, "date": "2025-09-26", "start_time": "08:00", "end_time": "17:00" }
     *   ]
     * }
     */
    public function showUserWorkLogs(int $userId): JsonResponse
    {
        $workLogs = $this->workLogUseCase->getWorkLogsByUserId($userId);
        return response()->json($workLogs);
    }
}
