<?php

namespace App\Modules\Forms\Domain\Entities;

use App\Models\FormResponse; // Ajusta el namespace de tu modelo Eloquent real

class FormResponseEntity
{
    private ?int $id;

    private int $form_id;

    private int $user_id;

    private ?int $batch_id;

    private string $status;

    private ?string $submitted_at;

    private ?int $reviewed_by;

    private ?string $reviewed_at;

    private ?string $review_notes;

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
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFormId(): int
    {
        return $this->form_id;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getBatchId(): ?int
    {
        return $this->batch_id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubmittedAt(): ?string
    {
        return $this->submitted_at;
    }

    public function getReviewedBy(): ?int
    {
        return $this->reviewed_by;
    }

    public function getReviewedAt(): ?string
    {
        return $this->reviewed_at;
    }

    public function getReviewNotes(): ?string
    {
        return $this->review_notes;
    }

    /* ============================
     * Setters
     * ============================ */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function setFormId(int $form_id): void
    {
        $this->form_id = $form_id;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setBatchId(?int $batch_id): void
    {
        $this->batch_id = $batch_id;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setSubmittedAt(?string $submitted_at): void
    {
        $this->submitted_at = $submitted_at;
    }

    public function setReviewedBy(?int $reviewed_by): void
    {
        $this->reviewed_by = $reviewed_by;
    }

    public function setReviewedAt(?string $reviewed_at): void
    {
        $this->reviewed_at = $reviewed_at;
    }

    public function setReviewNotes(?string $review_notes): void
    {
        $this->review_notes = $review_notes;
    }

    /* ============================
     * Conversión desde array
     * ============================ */
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

    /* ============================
     * Conversión a array
     * ============================ */
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

    /* ============================
     * Conversión desde modelo Eloquent
     * ============================ */
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
