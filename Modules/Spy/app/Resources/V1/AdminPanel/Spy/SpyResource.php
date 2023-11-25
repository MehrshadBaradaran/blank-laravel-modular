<?php

namespace Modules\Spy\app\Resources\V1\AdminPanel\Spy;

use Illuminate\Http\Resources\Json\JsonResource;

class SpyResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'ip_address' => $this->ip_address,

            'title' => $this->title,
            'action' => $this->action->value,
            'request_method' => $this->request_method,
            'request_url' => $this->request_url,
            'description' => $this->description,

            'date' => $this->created_at,

            'permission' => $this->permission_data_array,
            'target' => $this->target_data_array,

            'target_data' => $this->target_data,
            'user' => $this->user_data,
            'request_data' => $this->request_data,
            'request_device_data' => $this->request_device_data,
        ];
    }
}
