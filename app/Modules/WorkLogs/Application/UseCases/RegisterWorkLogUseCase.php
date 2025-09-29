<?php

namespace App\Modules\WorkLogs\Application\UseCases;

use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Domain\Services\WorkLogService;

/**
 * Caso de uso para registrar la hora de entrada o salida de un trabajador.
 *
 * Este caso de uso delega la lógica al servicio WorkLogService, que decide si debe crear un nuevo registro
 * (hora de entrada) o actualizar uno existente (hora de salida) para el usuario en la fecha actual.
 */
class RegisterWorkLogUseCase
{
    /**
     * @var WorkLogService Servicio de gestión de registros de trabajo.
     */
    private WorkLogService $workLogService;

    /**
     * Constructor del caso de uso.
     *
     * @param WorkLogService $workLogService Servicio de registros de trabajo.
     */
    public function __construct(WorkLogService $workLogService)
    {
        $this->workLogService = $workLogService;
    }

    /**
     * Ejecuta el registro automático de la hora de entrada o salida para el usuario indicado.
     *
     * @param int $userId ID del usuario.
     * @return WorkLogEntity Entidad del registro de trabajo creado o actualizado.
     */
    public function execute(int $userId): WorkLogEntity
    {
        return $this->workLogService->registerWorkLog($userId);
    }
}
