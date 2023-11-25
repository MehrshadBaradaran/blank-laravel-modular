<?php

namespace Modules\Banner\app\Resources\V1\AdminPanel\Banner;

use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'title' => $this->title,
            'url' => $this->url,

            'status' => $this->status->getBoolValue(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'cover' => $this->cover,
        ];
    }
}
