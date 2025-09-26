<?php

namespace App\Modules\Notifications\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'is_read' => (bool) $this->is_read,
            'related_table' => $this->related_table,
            'related_id' => $this->related_id,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
        ];
    }
}
