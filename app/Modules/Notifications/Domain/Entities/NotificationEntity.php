<?php

namespace App\Modules\Notifications\Domain\Entities;


class NotificationEntity
{
    private ?int $id;
    private int $user_id;
    private string $title;
    private string $message;
    private string $type;
    private bool $is_read;
    private ?string $related_table;
    private ?int $related_id;
    private ?string $expires_at;

    public function __construct(
        ?int $id,
        int $user_id,
        string $title,
        string $message,
        string $type,
        bool $is_read,
        ?string $related_table = null,
        ?int $related_id = null,
        ?string $expires_at = null
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->is_read = $is_read;
        $this->related_table = $related_table;
        $this->related_id = $related_id;
        $this->expires_at = $expires_at;
    }

    // Getters and setters...

    public function getId(): ?int
    {
        return $this->id;
    }
    public function getUserId(): int
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
    public function getType(): string
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
    public function getExpiresAt(): ?string
    {
        return $this->expires_at;
    }
    public function markAsRead(): void
    {
        $this->is_read = true;
    }
}
