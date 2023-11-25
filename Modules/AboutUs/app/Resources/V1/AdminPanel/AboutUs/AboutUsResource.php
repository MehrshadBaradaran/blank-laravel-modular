<?php

namespace Modules\AboutUs\app\Resources\V1\AdminPanel\AboutUs;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'updated_at' => $this->updated_at,

            'data' => $this->data,
        ];
    }
}
