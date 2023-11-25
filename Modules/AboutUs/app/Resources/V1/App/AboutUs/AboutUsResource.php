<?php

namespace Modules\AboutUs\app\Resources\V1\App\AboutUs;

use Illuminate\Http\Resources\Json\JsonResource;

class AboutUsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
