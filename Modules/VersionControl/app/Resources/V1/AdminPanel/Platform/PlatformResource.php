<?php

namespace Modules\VersionControl\app\Resources\V1\AdminPanel\Platform;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'url' => $this->url,
            'os' => $this->os,

            'status' => $this->status->getBoolValue(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'cover' => $this->cover,
        ];
    }
}
