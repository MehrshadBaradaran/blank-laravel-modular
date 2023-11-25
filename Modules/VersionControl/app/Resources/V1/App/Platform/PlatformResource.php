<?php

namespace Modules\VersionControl\app\Resources\V1\App\Platform;

use Illuminate\Http\Resources\Json\JsonResource;

class PlatformResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'title' => $this->title,
            'url' => $this->url,
            'os' => $this->os,

            'cover' => $this->cover,
        ];
    }
}
