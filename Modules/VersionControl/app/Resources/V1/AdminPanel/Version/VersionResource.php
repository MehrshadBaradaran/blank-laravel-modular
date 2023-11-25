<?php

namespace Modules\VersionControl\app\Resources\V1\AdminPanel\Version;

use Illuminate\Http\Resources\Json\JsonResource;

class VersionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'version_number' => $this->version_number,
            'description' => $this->description,

            'force_update' => $this->force_update,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'platform' => $this->platform_obj,
        ];
    }
}
