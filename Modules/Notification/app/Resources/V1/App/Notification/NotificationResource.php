<?php

namespace Modules\Notification\app\Resources\V1\App\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'body' => $this->body,

            'inform_type' => $this->inform_type,

            'is_read' => $this->is_read,

            'date' => $this->created_at,
        ];
    }
}
