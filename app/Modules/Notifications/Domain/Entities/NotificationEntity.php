<?php

namespace App\Modules\Notifications\Domain\Entities;

use Carbon\Carbon;
use DateTimeInterface;

class NotificationEntity
{
    private ?int $id;

    private string $title;

    private string $message;

    private ?string $type;

    private ?string $scope;

    private bool $is_read;

    private ?string $related_table;

    private ?int $related_id;

    private ?string $user_id;

    private ?DateTimeInterface $expires_at;

    public function __construct(
        ?int $id,
        string $title,
        string $message,
        ?string $type = null,
        ?string $scope = null,
        bool $is_read = false,
        ?string $related_table = null,
        ?int $related_id = null,
        ?string $user_id = null,
        DateTimeInterface|string|null $expires_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->scope = $scope;
        $this->is_read = $is_read;
        $this->related_table = $related_table;
        $this->related_id = $related_id;
        $this->user_id = $user_id;
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

    public function getScope(): ?string
    {
        return $this->scope;
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

    public function getUserId(): ?string
    {
        return $this->user_id;
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

    public function setScope(?string $scope): void
    {
        $this->scope = $scope;
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
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'scope' => $this->scope,
            'is_read' => $this->is_read,
            'related_table' => $this->related_table,
            'related_id' => $this->related_id,
            'user_id' => $this->user_id,
            'expires_at' => $this->expires_at ? $this->expires_at->format('Y-m-d H:i:s') : null,
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'],
            $data['message'],
            $data['type'] ?? null,
            $data['scope'] ?? null,
            $data['is_read'] ?? false,
            $data['related_table'] ?? null, // ahora va en la posición correcta
            $data['related_id'] ?? null,    // posición correcta
            $data['user_id'] ?? null,       // posición correcta
            $data['expires_at'] ?? null
        );
    }
}
