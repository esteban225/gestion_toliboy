<?php

namespace App\Modules\Notifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        // si la entidad tiene toArray:
        if (method_exists($this->resource, 'toArray')) {
            return $this->resource->toArray();
        }

        // o usar getters
        return [
            'id' => $this->resource->getId(),
            'user_id' => $this->resource->getUserId(),
            'title' => $this->resource->getTitle(),
            'message' => $this->resource->getMessage(),
            'type' => $this->resource->getType(),
            'is_read' => $this->resource->isRead(),
            'related_table' => $this->resource->getRelatedTable(),
            'related_id' => $this->resource->getRelatedId(),
            'expires_at' => $this->resource->getExpiresAt()?->format('Y-m-d H:i:s'),
        ];
    }
}
