<?php

namespace App\Modules\Forms\Domain\Entities;

use App\Models\FormResponse; // Ajusta el namespace de tu modelo Eloquent real

/**
 * Entidad que representa una respuesta a un formulario en el dominio.
 *
 * Esta clase encapsula los datos y comportamientos asociados a las respuestas
 * de formularios en el sistema, manteniendo la lógica de negocio independiente
 * de la implementación de persistencia.
 */
class FormResponseEntity
{
    /**
     * Identificador único de la respuesta del formulario
     */
    private ?int $id;

    /**
     * Identificador del formulario al que pertenece esta respuesta
     */
    private int $form_id;

    /**
     * Identificador del usuario que completó esta respuesta
     */
    private int $user_id;

    /**
     * Identificador del lote al que está asociado esta respuesta (opcional)
     */
    private ?int $batch_id;

    /**
     * Estado actual de la respuesta (pending, in_progress, completed, approved, rejected)
     */
    private string $status;

    /**
     * Fecha y hora en que se envió la respuesta completada
     */
    private ?string $submitted_at;

    /**
     * Identificador del usuario que revisó esta respuesta
     */
    private ?int $reviewed_by;

    /**
     * Fecha y hora en que se revisó esta respuesta
     */
    private ?string $reviewed_at;

    /**
     * Notas o comentarios adicionales de la revisión
     */
    private ?string $review_notes;

    /**
     * Constructor de la entidad FormResponse
     *
     * @param  int|null  $id  Identificador único de la respuesta
     * @param  int  $form_id  Identificador del formulario
     * @param  int  $user_id  Identificador del usuario que completó la respuesta
     * @param  int|null  $batch_id  Identificador del lote asociado (opcional)
     * @param  string  $status  Estado de la respuesta
     * @param  string|null  $submitted_at  Fecha de envío (opcional)
     * @param  int|null  $reviewed_by  ID del revisor (opcional)
     * @param  string|null  $reviewed_at  Fecha de revisión (opcional)
     * @param  string|null  $review_notes  Comentarios de revisión (opcional)
     */
    public function __construct(
        ?int $id,
        int $form_id,
        int $user_id,
        ?int $batch_id,
        string $status,
        ?string $submitted_at,
        ?int $reviewed_by,
        ?string $reviewed_at,
        ?string $review_notes
    ) {
        $this->id = $id;
        $this->form_id = $form_id;
        $this->user_id = $user_id;
        $this->batch_id = $batch_id;
        $this->status = $status;
        $this->submitted_at = $submitted_at;
        $this->reviewed_by = $reviewed_by;
        $this->reviewed_at = $reviewed_at;
        $this->review_notes = $review_notes;
    }

    /* ============================
     * Getters
     * ============================ */

    /**
     * Obtiene el ID de la respuesta
     *
     * @return int|null El ID o null si es una respuesta nueva
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Obtiene el ID del formulario asociado
     *
     * @return int ID del formulario
     */
    public function getFormId(): int
    {
        return $this->form_id;
    }

    /**
     * Obtiene el ID del usuario que completó la respuesta
     *
     * @return int ID del usuario
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * Obtiene el ID del lote asociado a esta respuesta
     *
     * @return int|null ID del lote o null si no está asociado
     */
    public function getBatchId(): ?int
    {
        return $this->batch_id;
    }

    /**
     * Obtiene el estado actual de la respuesta
     *
     * @return string Estado (pending, in_progress, completed, approved, rejected)
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Obtiene la fecha de envío de la respuesta
     *
     * @return string|null Fecha de envío o null si no ha sido enviado
     */
    public function getSubmittedAt(): ?string
    {
        return $this->submitted_at;
    }

    /**
     * Obtiene el ID del usuario que revisó la respuesta
     *
     * @return int|null ID del revisor o null si no ha sido revisado
     */
    public function getReviewedBy(): ?int
    {
        return $this->reviewed_by;
    }

    /**
     * Obtiene la fecha de revisión de la respuesta
     *
     * @return string|null Fecha de revisión o null si no ha sido revisado
     */
    public function getReviewedAt(): ?string
    {
        return $this->reviewed_at;
    }

    /**
     * Obtiene las notas de revisión de la respuesta
     *
     * @return string|null Notas de revisión o null si no hay notas
     */
    public function getReviewNotes(): ?string
    {
        return $this->review_notes;
    }

    /* ============================
     * Setters
     * ============================ */

    /**
     * Establece el ID de la respuesta
     *
     * @param  int|null  $id  El ID a establecer o null para respuesta nueva
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Establece el ID del formulario asociado
     *
     * @param  int  $form_id  ID del formulario
     */
    public function setFormId(int $form_id): void
    {
        $this->form_id = $form_id;
    }

    /**
     * Establece el ID del usuario que completó la respuesta
     *
     * @param  int  $user_id  ID del usuario
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * Establece el ID del lote asociado a esta respuesta
     *
     * @param  int|null  $batch_id  ID del lote o null si no está asociado
     */
    public function setBatchId(?int $batch_id): void
    {
        $this->batch_id = $batch_id;
    }

    /**
     * Establece el estado de la respuesta
     *
     * @param  string  $status  Estado (pending, in_progress, completed, approved, rejected)
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * Establece la fecha de envío de la respuesta
     *
     * @param  string|null  $submitted_at  Fecha de envío o null
     */
    public function setSubmittedAt(?string $submitted_at): void
    {
        $this->submitted_at = $submitted_at;
    }

    /**
     * Establece el ID del usuario que revisó la respuesta
     *
     * @param  int|null  $reviewed_by  ID del revisor o null
     */
    public function setReviewedBy(?int $reviewed_by): void
    {
        $this->reviewed_by = $reviewed_by;
    }

    /**
     * Establece la fecha de revisión de la respuesta
     *
     * @param  string|null  $reviewed_at  Fecha de revisión o null
     */
    public function setReviewedAt(?string $reviewed_at): void
    {
        $this->reviewed_at = $reviewed_at;
    }

    /**
     * Establece las notas de revisión de la respuesta
     *
     * @param  string|null  $review_notes  Notas de revisión o null
     */
    public function setReviewNotes(?string $review_notes): void
    {
        $this->review_notes = $review_notes;
    }

    /**
     * Crea una instancia de la entidad a partir de un array asociativo
     *
     * @param  array  $data  Array con los datos de la respuesta del formulario
     * @return self Nueva instancia de la entidad
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['form_id'],
            $data['user_id'],
            $data['batch_id'] ?? null,
            $data['status'],
            $data['submitted_at'] ?? null,
            $data['reviewed_by'] ?? null,
            $data['reviewed_at'] ?? null,
            $data['review_notes'] ?? null
        );
    }

    /**
     * Convierte la entidad a un array asociativo
     *
     * @return array Array con los datos de la entidad
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'form_id' => $this->form_id,
            'user_id' => $this->user_id,
            'batch_id' => $this->batch_id,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at,
            'reviewed_by' => $this->reviewed_by,
            'reviewed_at' => $this->reviewed_at,
            'review_notes' => $this->review_notes,
        ];
    }

    /**
     * Crea una instancia de la entidad a partir de un modelo Eloquent
     *
     * @param  FormResponse  $model  Modelo Eloquent de FormResponse
     * @return self Nueva instancia de la entidad
     */
    public static function fromModel(FormResponse $model): self
    {
        return new self(
            $model->id,
            $model->form_id,
            $model->user_id,
            $model->batch_id,
            $model->status,
            $model->submitted_at ? (string) $model->submitted_at : null,
            $model->reviewed_by,
            $model->reviewed_at ? (string) $model->reviewed_at : null,
            $model->review_notes
        );
    }
}
