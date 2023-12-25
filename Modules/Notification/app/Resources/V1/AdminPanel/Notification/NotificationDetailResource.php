<?php

namespace Modules\Notification\app\Resources\V1\AdminPanel\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'body' => $this->body,

            'type' => $this->type->value,
            'inform_type' => $this->inform_type->value,

            'general' => $this->general,
            'status' => $this->status->getBoolValue(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'users' => $this->users_data,
        ];
    }
}
