<?php

namespace App\Modules\WorkLogs\Domain\Services;

use App\Modules\WorkLogs\Application\DTOs\WorkLogDTO;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Domain\Repositories\WorkLogRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

/**
 * Servicio para la gestión de registros de trabajo (WorkLogs).
 *
 * Esta clase actúa como intermediaria entre los casos de uso y el repositorio,
 * encapsulando la lógica de negocio relacionada con los registros de trabajo.
 */
class WorkLogService
{
    /**
     * @var WorkLogRepositoryI Repositorio para interactuar con la capa de persistencia.
     */
    private WorkLogRepositoryI $workLogRepository;

    /**
     * Constructor del servicio.
     *
     * @param  WorkLogRepositoryI  $workLogRepository  Repositorio de registros de trabajo.
     */
    public function __construct(WorkLogRepositoryI $workLogRepository)
    {
        $this->workLogRepository = $workLogRepository;
    }

    /**
     * Obtiene todos los registros de trabajo.
     *
     * @return WorkLogEntity[] Lista de entidades de registros de trabajo.
     */
    public function getAllWorkLogs(): array
    {
        return $this->workLogRepository->findAll();
    }

    /**
     * Obtiene los registros de trabajo paginados.
     *
     * @param  int  $perPage  Cantidad de registros por página.
     * @param  int  $page  Número de la página actual.
     */
    public function paginateWorkLogs(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return $this->workLogRepository->paginate($filters, $perPage);
    }

    /**
     * Obtiene un registro de trabajo por su ID.
     *
     * @param  int  $id  Identificador único del registro de trabajo.
     * @return WorkLogEntity|null La entidad del registro de trabajo o null si no se encuentra.
     */
    public function getWorkLogById(int $id): ?WorkLogEntity
    {
        return $this->workLogRepository->findById($id);
    }

    /**
     * Crea un nuevo registro de trabajo.
     *
     * @param  WorkLogDTO  $workLog  DTO del registro de trabajo a crear.
     * @return WorkLogEntity La entidad del registro de trabajo creada.
     */
    public function createWorkLog(WorkLogDTO $workLog): WorkLogEntity
    {
        return $this->workLogRepository->create($workLog);
    }

    /**
     * Actualiza un registro de trabajo existente.
     *
     * @param  WorkLogDTO  $workLog  DTO del registro de trabajo con los datos actualizados.
     * @return WorkLogEntity La entidad del registro de trabajo actualizado.
     */
    public function updateWorkLog(WorkLogDTO $workLog): WorkLogEntity
    {
        return $this->workLogRepository->update($workLog);
    }

    /**
     * Elimina un registro de trabajo por su ID.
     *
     * @param  int  $id  Identificador único del registro de trabajo a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function deleteWorkLog(int $id): bool
    {
        return $this->workLogRepository->delete($id);
    }

    /**
     * Obtiene los registros de trabajo asociados a un usuario por su ID.
     *
     * @param  int  $userId  Identificador único del usuario.
     * @return WorkLogEntity[] Lista de entidades de registros de trabajo asociados al usuario.
     */
    public function getWorkLogsByUserId(int $userId): array
    {
        return $this->workLogRepository->findByUserId($userId);
    }

    /**
     * Registra automáticamente la hora de entrada o salida del trabajador.
     *
     * Este método busca si existe un registro de trabajo para el usuario en la fecha actual.
     * - Si no existe, crea un nuevo registro con la hora de entrada actual.
     * - Si ya existe, actualiza la hora de salida y calcula las horas trabajadas y horas extra.
     * Las horas se guardan en formato H:i:s para compatibilidad con el tipo TIME en la base de datos.
     *
     * @param int $id ID del usuario.
     * @return WorkLogEntity La entidad del registro de trabajo creado o actualizado.
     */
    public function registerWorkLog(int $id): WorkLogEntity
    {
        // Busca si ya existe un registro para el usuario en la fecha actual
        $workLog = $this->workLogRepository->findByUserAndDate($id, date('Y-m-d'));

        Log::info("Buscando registro de trabajo para el usuario {$id} en la fecha " . date('Y-m-d'));
        Log::info("Buscando registro de trabajo para el usuario {$id} en la fecha " . date('Y-m-d') . ($workLog ? 'Encontrado' : 'No encontrado'));

        $currentTime = date('H:i');

        // Si no existe registro, crea uno nuevo con la hora de entrada
        if (! $workLog) {
            $workLogDTO = new WorkLogDTO(
                null,
                user_id: $id,
                date: date('Y-m-d'),
                start_time: $currentTime,
                end_time: null,
                total_hours: null,
                overtime_hours: null,
                batch_id: null,
                task_description: null,
                notes: null
            );

            return $this->workLogRepository->create($workLogDTO);
        }

        // Si ya existe, actualiza la hora de salida y calcula horas trabajadas
        // Calcula la diferencia en segundos entre hora de entrada y salida
        $startTime = strtotime($workLog->getStartTime());
        $endTime = strtotime($currentTime);
        $diff = $endTime - $startTime;

        // Convierte la diferencia a horas y minutos
        $hours = floor($diff / 3600);
        $minutes = floor(($diff % 3600) / 60);
        $totalHours = sprintf('%02d:%02d:%02d', $hours, $minutes, 0); // formato H:i:s

        // Calcula horas extra si se trabajó más de 8 horas
        $overtimeDecimal = ($hours + $minutes / 60) > 8 ? ($hours + $minutes / 60) - 8 : 0;
        $overtimeHours = $overtimeDecimal > 0
            ? sprintf('%02d:%02d:%02d', floor($overtimeDecimal), round(($overtimeDecimal - floor($overtimeDecimal)) * 60), 0)
            : '00:00:00';

        // Crea el DTO actualizado con los nuevos valores
        $workLogDTO = new WorkLogDTO(
            $workLog->getId(),
            user_id: $workLog->getUserId(),
            date: $workLog->getDate(),
            start_time: $workLog->getStartTime(),
            end_time: $currentTime,
            total_hours: $totalHours,
            overtime_hours: $overtimeHours,
            batch_id: $workLog->getBatchId(),
            task_description: $workLog->getTaskDescription(),
            notes: $workLog->getNotes()
        );

        // Actualiza el registro en la base de datos
        return $this->workLogRepository->update($workLogDTO);
    }
}
