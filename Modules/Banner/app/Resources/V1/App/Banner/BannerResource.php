<?php

namespace Modules\Banner\app\Resources\V1\App\Banner;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'url' => $this->url,

            'cover' => $this->cover,
        ];
    }
}
