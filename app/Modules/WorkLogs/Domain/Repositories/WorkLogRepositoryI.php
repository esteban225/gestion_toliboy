<?php

namespace App\Modules\WorkLogs\Domain\Repositories;

use App\Modules\WorkLogs\Application\DTOs\WorkLogDTO;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Interfaz para el repositorio de registros de trabajo (WorkLog).
 *
 * Define los métodos necesarios para interactuar con la capa de persistencia
 * de los registros de trabajo, siguiendo el principio de inversión de dependencias (DIP).
 */
interface WorkLogRepositoryI
{
    /**
     * Obtiene todos los registros de trabajo.
     *
     * @return WorkLogEntity[] Lista de entidades de registros de trabajo.
     */
    public function findAll(): array;

    /**
     * Obtiene los registros de trabajo paginados.
     *
     * @param  int  $perPage  Cantidad de registros por página.
     * @param  int  $page  Número de la página actual.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Busca un registro de trabajo por su ID.
     *
     * @param  int  $id  Identificador único del registro de trabajo.
     * @return WorkLogEntity|null La entidad del registro de trabajo o null si no se encuentra.
     */
    public function findById(int $id): ?WorkLogEntity;

    /**
     * Busca registros de trabajo por el ID del usuario.
     *
     * @param  int  $userId  Identificador único del usuario.
     * @return WorkLogEntity[] Lista de entidades de registros de trabajo asociados al usuario.
     */
    public function findByUserId(int $userId): array;

    /**
     * Crea un nuevo registro de trabajo.
     *
     * @param  WorkLogDTO  $workLog  Entidad del registro de trabajo a crear.
     * @return WorkLogEntity La entidad del registro de trabajo creada.
     */
    public function create(WorkLogDTO $workLog): WorkLogEntity;

    /**
     * Actualiza un registro de trabajo existente.
     *
     * @param  WorkLogDTO  $workLog  Entidad del registro de trabajo con los datos actualizados.
     * @return WorkLogEntity La entidad del registro de trabajo actualizada.
     */
    public function update(WorkLogDTO $workLog): ?WorkLogEntity;

    /**
     * Elimina un registro de trabajo por su ID.
     *
     * @param  int  $id  Identificador único del registro de trabajo a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function delete(int $id): bool;

    /**
     * Busca un registro de trabajo por el ID del usuario y la fecha.
     *
     * @param  int  $userId  Identificador único del usuario.
     * @param  string  $date  Fecha en formato 'Y-m-d'.
     * @return WorkLogEntity|null La entidad del registro de trabajo o null si no se encuentra.
     */
    public function findByUserAndDate(int $userId, string $date): ?WorkLogEntity;
}
