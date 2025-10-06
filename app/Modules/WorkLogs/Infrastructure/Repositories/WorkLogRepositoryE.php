<?php

namespace App\Modules\WorkLogs\Infrastructure\Repositories;

use App\Models\WorkLog;
use App\Modules\WorkLogs\Application\DTOs\WorkLogDTO;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Domain\Repositories\WorkLogRepositoryI;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repositorio Eloquent para la persistencia de registros de trabajo (WorkLog).
 *
 * Implementa la interfaz WorkLogRepositoryI y gestiona la interacción con la base de datos
 * usando el modelo Eloquent WorkLog. Convierte los modelos a entidades de dominio.
 */
class WorkLogRepositoryE implements WorkLogRepositoryI
{
    /**
     * Obtiene todos los registros de trabajo.
     *
     * @return WorkLogEntity[] Lista de entidades de registros de trabajo.
     */
    public function findAll(): array
    {
        $workLogModels = WorkLog::all();

        return $workLogModels->map(fn ($model) => $this->mapToEntity($model))->toArray();
    }

    /**
     * Obtiene los registros de trabajo paginados, aplicando filtros si es necesario.
     *
     * @param  array  $filters  Filtros de búsqueda.
     * @param  int  $perPage  Cantidad de registros por página.
     * @return LengthAwarePaginator Paginador de registros de trabajo.
     */
    public function paginate(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = WorkLog::query();
        foreach ($filters as $key => $value) {
            $query->where($key, 'LIKE', "%$value%");
        }

        return $query->paginate($perPage);
    }

    /**
     * Busca un registro de trabajo por su ID.
     *
     * @param  int  $id  Identificador único del registro de trabajo.
     * @return WorkLogEntity|null Entidad del registro o null si no se encuentra.
     */
    public function findById(int $id): ?WorkLogEntity
    {
        $workLogModel = WorkLog::find($id);
        if (! $workLogModel) {
            return null;
        }

        return $this->mapToEntity($workLogModel);
    }

    /**
     * Obtiene los registros de trabajo asociados a un usuario.
     *
     * @param  int  $userId  Identificador único del usuario.
     * @return WorkLogEntity[] Lista de entidades de registros de trabajo.
     */
    public function findByUserId(int $userId): array
    {
        $workLogModels = WorkLog::where('user_id', $userId)->get();

        return $workLogModels->map(fn ($model) => $this->mapToEntity($model))->toArray();
    }

    /**
     * Crea un nuevo registro de trabajo en la base de datos.
     *
     * @param  WorkLogDTO  $workLog  DTO con los datos del registro de trabajo.
     * @return WorkLogEntity Entidad del registro creado.
     */
    public function create(WorkLogDTO $workLog): WorkLogEntity
    {
        $workLogModel = WorkLog::create($workLog->toArray());
        $workLogModel->refresh(); // Recargar para obtener columnas generadas

        return $this->mapToEntity($workLogModel);
    }

    /**
     * Actualiza un registro de trabajo existente.
     *
     * @param  WorkLogDTO  $workLog  DTO con los datos actualizados.
     * @return WorkLogEntity|null Entidad actualizada o null si no se encuentra.
     */
    public function update(WorkLogDTO $workLog): ?WorkLogEntity
    {
        $workLogModel = WorkLog::find($workLog->id);
        if (! $workLogModel) {
            return null;
        }
        $workLogModel->update($workLog->toArray());

        return $this->mapToEntity($workLogModel);
    }

    /**
     * Elimina un registro de trabajo por su ID.
     *
     * @param  int  $id  Identificador único del registro de trabajo.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function delete(int $id): bool
    {
        $workLogModel = WorkLog::find($id);
        if (! $workLogModel) {
            return false;
        }

        return $workLogModel->delete();
    }

    /**
     * Busca un registro de trabajo por usuario y fecha.
     *
     * @param  int  $userId  Identificador único del usuario.
     * @param  string  $date  Fecha del registro (formato Y-m-d).
     * @return WorkLogEntity|null Entidad encontrada o null si no existe.
     */
    public function findByUserAndDate(int $userId, string $date): ?WorkLogEntity
    {
        $workLog = WorkLog::where('user_id', $userId)
            ->where('date', $date)
            ->first();
        if (! $workLog) {
            return null;
        }

        return $this->mapToEntity($workLog);
    }

    /**
     * Mapea un modelo Eloquent a una entidad de dominio WorkLogEntity.
     *
     * @param  WorkLog  $workLogModel  Modelo Eloquent de WorkLog.
     * @return WorkLogEntity Entidad de dominio WorkLogEntity.
     */
    private function mapToEntity(WorkLog $workLogModel): WorkLogEntity
    {
        return new WorkLogEntity(
            id: $workLogModel->id,
            user_id: $workLogModel->user_id,
            date: $workLogModel->date,
            start_time: $workLogModel->start_time,
            end_time: $workLogModel->end_time,
            total_hours: $workLogModel->total_hours,       // columna generada
            overtime_hours: $workLogModel->overtime_hours, // columna generada
            batch_id: $workLogModel->batch_id,
            task_description: $workLogModel->task_description,
            notes: $workLogModel->notes,
        );
    }
}
