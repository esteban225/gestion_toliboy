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
    private ?string $related_table;
    private ?int $related_id;
    private ?DateTimeInterface $expires_at;

    public function __construct(
        ?int $id,
        string $title,
        string $message,
        ?string $type = null,
        ?string $scope = null,
        ?string $related_table = null,
        ?int $related_id = null,
        DateTimeInterface|string|null $expires_at = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->scope = $scope;
        $this->related_table = $related_table;
        $this->related_id = $related_id;
        $this->expires_at = $this->normalizeExpiresAt($expires_at);
    }

    private function normalizeExpiresAt(DateTimeInterface|string|null $value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }

        return $value ? Carbon::parse($value) : null;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['title'],
            $data['message'],
            $data['type'] ?? null,
            $data['scope'] ?? null,
            $data['related_table'] ?? null,
            $data['related_id'] ?? null,
            $data['expires_at'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'scope' => $this->scope,
            'related_table' => $this->related_table,
            'related_id' => $this->related_id,
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
        ];
    }

    // getters...

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
    public function isGlobal(): bool
    {
        return $this->scope === 'global';
    }

    public function setScope (string $scope): void
    {
        $this->scope = $scope;
    }

    public function setExpiresAt(?DateTimeInterface $expiresAt): void
    {
        $this->expires_at = $expiresAt;
    }
}