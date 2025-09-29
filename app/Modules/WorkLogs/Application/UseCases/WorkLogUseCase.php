<?php


/**
 * Caso de uso para la gestión de registros de trabajo (WorkLogs).
 * Permite crear, actualizar, eliminar, consultar y paginar registros de horas trabajadas,
 * así como registrar automáticamente la hora de entrada/salida.
 *
 * @package App\Modules\WorkLogs\Application\UseCases
 */
namespace App\Modules\WorkLogs\Application\UseCases;

use App\Modules\WorkLogs\Application\DTOs\WorkLogDTO;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Domain\Services\WorkLogService;
use Illuminate\Pagination\LengthAwarePaginator;

class WorkLogUseCase
{
    /**
     * Servicio de lógica de negocio para WorkLogs.
     * @var WorkLogService
     */
    private WorkLogService $workLogService;

    /**
     * Constructor de WorkLogUseCase.
     *
     * @param WorkLogService $workLogService Servicio de lógica de negocio para WorkLogs.
     */
    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    /**
     * Crea un nuevo registro de trabajo.
     *
     * @param WorkLogDTO $workLog DTO con los datos del registro de trabajo.
     * @return WorkLogEntity Entidad del registro creado.
     */
    public function createWorkLog(WorkLogDTO $workLog): WorkLogEntity
    {
        $workLogModel = $this->workLogService->createWorkLog($workLog);
        // Aquí puedes llamar al servicio de notificaciones si es necesario
        return $workLogModel;
    }

    /**
     * Actualiza un registro de trabajo existente.
     *
     * @param WorkLogDTO $workLog DTO con los datos actualizados.
     * @return WorkLogEntity Entidad del registro actualizado.
     */
    public function updateWorkLog(WorkLogDTO $workLog): WorkLogEntity
    {
        $workLogModel = $this->workLogService->updateWorkLog($workLog);
        // Aquí puedes llamar al servicio de notificaciones si es necesario
        return $workLogModel;
    }

    /**
     * Elimina un registro de trabajo por su ID.
     *
     * @param int $id ID del registro a eliminar.
     * @return bool True si se eliminó correctamente, false en caso contrario.
     */
    public function deleteWorkLog(int $id): bool
    {
        $result = $this->workLogService->deleteWorkLog($id);
        // Aquí puedes llamar al servicio de notificaciones si es necesario
        return $result;
    }

    /**
     * Obtiene un registro de trabajo por su ID.
     *
     * @param int $id ID del registro.
     * @return WorkLogEntity|null Entidad del registro si existe, null si no se encuentra.
     */
    public function getWorkLogById(int $id): ?WorkLogEntity
    {
        return $this->workLogService->getWorkLogById($id);
    }

    /**
     * Obtiene todos los registros de trabajo de un usuario.
     *
     * @param int $userId ID del usuario.
     * @return array Lista de entidades WorkLog del usuario.
     */
    public function getWorkLogsByUserId(int $userId): array
    {
        return $this->workLogService->getWorkLogsByUserId($userId);
    }

    /**
     * Obtiene todos los registros de trabajo.
     *
     * @return array Lista de todas las entidades WorkLog.
     */
    public function getAllWorkLogs(): array
    {
        return $this->workLogService->getAllWorkLogs();
    }

}
