<?php

namespace App\Modules\Notifications\Domain\Entities;

use Carbon\Carbon;
use DateTimeInterface;

class NotificationEntity
{
    private ?int $id;

    private ?int $user_id;

    private string $title;

    private string $message;

    private ?string $type;

    private bool $is_read;

    private ?string $related_table;

    private ?int $related_id;

    private ?DateTimeInterface $expires_at;

    public function __construct(
        ?int $id,
        ?int $user_id,
        string $title,
        string $message,
        ?string $type = null,
        bool $is_read = false,
        ?string $related_table = null,
        ?int $related_id = null,
        DateTimeInterface|string|null $expires_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->is_read = $is_read;
        $this->related_table = $related_table;
        $this->related_id = $related_id;
        $this->expires_at = $this->normalizeExpiresAt($expires_at);
    }

    private function normalizeExpiresAt(DateTimeInterface|string|null $value): ?DateTimeInterface
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        return Carbon::parse($value);
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isRead(): bool
    {
        return $this->is_read;
    }

    public function getRelatedTable(): ?string
    {
        return $this->related_table;
    }

    public function getRelatedId(): ?int
    {
        return $this->related_id;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expires_at;
    }

    // Mutators / domain behaviour
    public function markAsRead(): void
    {
        $this->is_read = true;
    }

    public function setIsRead(bool $v): void
    {
        $this->is_read = $v;
    }

    /**
     * Setea expires_at. Acepta DateTimeInterface, string parseable, o null.
     * Si pasas null y quieres un TTL por defecto, aplica desde el servicio antes de persistir.
     */
    public function setExpiresAt(DateTimeInterface|string|null $expires_at = null): void
    {
        $this->expires_at = $this->normalizeExpiresAt($expires_at);
    }

    /**
     * Inmutable: devuelve nueva instancia con expires_at cambiado.
     */
    public function withExpiresAt(DateTimeInterface|string|null $expires_at = null): self
    {
        $clone = clone $this;
        $clone->setExpiresAt($expires_at);

        return $clone;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'is_read' => $this->is_read,
            'related_table' => $this->related_table,
            'related_id' => $this->related_id,
            'expires_at' => $this->expires_at ? $this->expires_at->format('Y-m-d H:i:s') : null,
        ];
    }
}
