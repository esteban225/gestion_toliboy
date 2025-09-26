<?php

namespace App\Modules\WorkLogs\Infrastructure\Repositories;

use App\Models\WorkLog;
use App\Modules\WorkLogs\Domain\Entities\WorkLogEntity;
use App\Modules\WorkLogs\Domain\Repositories\WorkLogRepositoryI;

class WorkLogRepositoryE implements WorkLogRepositoryI
{
    public function create(WorkLogEntity $workLog): WorkLogEntity
    {
        $workLogModel = new WorkLog;
        $workLogModel->user_id = $workLog->user_id;
        $workLogModel->date = $workLog->date;
        $workLogModel->start_time = $workLog->start_time;
        $workLogModel->end_time = $workLog->end_time;
        $workLogModel->batch_id = $workLog->batch_id;
        $workLogModel->task_description = $workLog->task_description;
        $workLogModel->notes = $workLog->notes;
        $workLogModel->save();

        // 🔄 Recargar el modelo para obtener las columnas generadas
        $workLogModel->refresh();

        return $this->mapToEntity($workLogModel);
    }

    public function update(WorkLogEntity $workLog): WorkLogEntity
    {
        $workLogModel = WorkLog::find($workLog->id);
        if (! $workLogModel) {
            throw new \Exception('WorkLog not found');
        }

        $workLogModel->user_id = $workLog->user_id;
        $workLogModel->date = $workLog->date;
        $workLogModel->start_time = $workLog->start_time;
        $workLogModel->end_time = $workLog->end_time;
        $workLogModel->batch_id = $workLog->batch_id;
        $workLogModel->task_description = $workLog->task_description;
        $workLogModel->notes = $workLog->notes;
        $workLogModel->save();

        // 🔄 Recargar el modelo para obtener las columnas generadas
        $workLogModel->refresh();

        return $this->mapToEntity($workLogModel);
    }

    public function delete(int $id): bool
    {
        $workLogModel = WorkLog::find($id);
        if (! $workLogModel) {
            return false;
        }

        return $workLogModel->delete();
    }

    public function findById(int $id): ?WorkLogEntity
    {
        $workLogModel = WorkLog::find($id);
        if (! $workLogModel) {
            return null;
        }

        return $this->mapToEntity($workLogModel);
    }

    public function findByUserId(int $userId): array
    {
        $workLogModels = WorkLog::where('user_id', $userId)->get();

        return $workLogModels->map(fn ($model) => $this->mapToEntity($model))->toArray();
    }

    public function findAll(): array
    {
        $workLogModels = WorkLog::all();

        return $workLogModels->map(fn ($model) => $this->mapToEntity($model))->toArray();
    }

    /**
     * Mapea un modelo Eloquent a una entidad de dominio
     */
    private function mapToEntity(WorkLog $workLogModel): WorkLogEntity
    {
        return new WorkLogEntity(
            id: $workLogModel->id,
            user_id: $workLogModel->user_id,
            date: $workLogModel->date,
            start_time: $workLogModel->start_time,
            end_time: $workLogModel->end_time,
            total_hours: $workLogModel->total_hours,       // ← columna generada
            overtime_hours: $workLogModel->overtime_hours, // ← columna generada
            batch_id: $workLogModel->batch_id,
            task_description: $workLogModel->task_description,
            notes: $workLogModel->notes,
        );
    }
}
