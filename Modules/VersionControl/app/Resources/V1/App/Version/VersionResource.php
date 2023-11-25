<?php

namespace Modules\VersionControl\app\Resources\V1\App\Version;


use Illuminate\Http\Resources\Json\JsonResource;

class VersionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'version_number' => $this->version_number,
            'description' => $this->description,

            'force_update' => $this->force_update,
        ];
    }
}
