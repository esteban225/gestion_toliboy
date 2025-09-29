<?php

namespace App\Modules\WorkLogs\Domain\Entities;

/**
 * Entidad que representa un registro de trabajo (WorkLog).
 *
 * Esta clase encapsula los datos y comportamientos relacionados con un registro de trabajo.
 * Implementa métodos para acceder y modificar los datos, así como para convertirlos a y desde arrays.
 */
class WorkLogEntity
{
    /**
     * @var int|null Identificador único del registro de trabajo.
     */
    private ?int $id;

    /**
     * @var int Identificador del usuario asociado al registro.
     */
    private int $user_id;

    /**
     * @var string|null Fecha del registro de trabajo.
     */
    private ?string $date;

    /**
     * @var string|null Hora de inicio del trabajo.
     */
    private ?string $start_time;

    /**
     * @var string|null Hora de finalización del trabajo.
     */
    private ?string $end_time;

    /**
     * @var string|null Total de horas trabajadas.
     */
    private ?string $total_hours;

    /**
     * @var string|null Total de horas extra trabajadas.
     */
    private ?string $overtime_hours;

    /**
     * @var string|null Identificador del lote asociado al registro.
     */
    private ?string $batch_id;

    /**
     * @var string|null Descripción de la tarea realizada.
     */
    private ?string $task_description;

    /**
     * @var string|null Notas adicionales sobre el registro de trabajo.
     */
    private ?string $notes;

    /**
     * Constructor de la entidad WorkLogEntity.
     *
     * @param  int|null  $id  Identificador único del registro de trabajo.
     * @param  int  $user_id  Identificador del usuario asociado al registro.
     * @param  string|null  $date  Fecha del registro de trabajo.
     * @param  string|null  $start_time  Hora de inicio del trabajo.
     * @param  string|null  $end_time  Hora de finalización del trabajo.
     * @param  string|null  $total_hours  Total de horas trabajadas.
     * @param  string|null  $overtime_hours  Total de horas extra trabajadas.
     * @param  string|null  $batch_id  Identificador del lote asociado al registro.
     * @param  string|null  $task_description  Descripción de la tarea realizada.
     * @param  string|null  $notes  Notas adicionales sobre el registro de trabajo.
     */
    public function __construct(
        ?int $id,
        int $user_id,
        ?string $date,
        ?string $start_time,
        ?string $end_time,
        ?string $total_hours,
        ?string $overtime_hours,
        ?string $batch_id,
        ?string $task_description,
        ?string $notes,
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->date = $date;
        $this->start_time = $start_time;
        $this->end_time = $end_time;
        $this->total_hours = $total_hours;
        $this->overtime_hours = $overtime_hours;
        $this->batch_id = $batch_id;
        $this->task_description = $task_description;
        $this->notes = $notes;
    }

    // Getters

    /**
     * Obtiene el identificador único del registro de trabajo.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtiene el identificador del usuario asociado al registro.
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Obtiene la fecha del registro de trabajo.
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * Obtiene la hora de inicio del trabajo.
     */
    public function getStartTime(): ?string
    {
        return $this->start_time;
    }

    /**
     * Obtiene la hora de finalización del trabajo.
     */
    public function getEndTime(): ?string
    {
        return $this->end_time;
    }

    /**
     * Obtiene el total de horas trabajadas.
     */
    public function getTotalHours(): ?string
    {
        return $this->total_hours;
    }

    /**
     * Obtiene el total de horas extra trabajadas.
     */
    public function getOvertimeHours(): ?string
    {
        return $this->overtime_hours;
    }

    /**
     * Obtiene el identificador del lote asociado al registro.
     */
    public function getBatchId(): ?string
    {
        return $this->batch_id;
    }

    /**
     * Obtiene la descripción de la tarea realizada.
     */
    public function getTaskDescription(): ?string
    {
        return $this->task_description;
    }

    /**
     * Obtiene las notas adicionales sobre el registro de trabajo.
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    // Setters

    /**
     * Establece el identificador único del registro de trabajo.
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Establece el identificador del usuario asociado al registro.
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * Establece la fecha del registro de trabajo.
     */
    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

    /**
     * Establece la hora de inicio del trabajo.
     */
    public function setStartTime(?string $start_time): void
    {
        $this->start_time = $start_time;
    }

    /**
     * Establece la hora de finalización del trabajo.
     */
    public function setEndTime(?string $end_time): void
    {
        $this->end_time = $end_time;
    }

    /**
     * Establece el total de horas trabajadas.
     */
    public function setTotalHours(?string $total_hours): void
    {
        $this->total_hours = $total_hours;
    }

    /**
     * Establece el total de horas extra trabajadas.
     */
    public function setOvertimeHours(?string $overtime_hours): void
    {
        $this->overtime_hours = $overtime_hours;
    }

    /**
     * Establece el identificador del lote asociado al registro.
     */
    public function setBatchId(?string $batch_id): void
    {
        $this->batch_id = $batch_id;
    }

    /**
     * Establece la descripción de la tarea realizada.
     */
    public function setTaskDescription(?string $task_description): void
    {
        $this->task_description = $task_description;
    }

    /**
     * Establece las notas adicionales sobre el registro de trabajo.
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    // Métodos estáticos

    /**
     * Crea una instancia de WorkLogEntity a partir de un array de datos.
     *
     * @param  array  $data  Datos para inicializar la entidad.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
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
    }

    /**
     * Convierte la entidad WorkLogEntity a un array de datos.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'date' => $this->date,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'total_hours' => $this->total_hours,
            'overtime_hours' => $this->overtime_hours,
            'batch_id' => $this->batch_id,
            'task_description' => $this->task_description,
            'notes' => $this->notes,
        ];
    }
}
