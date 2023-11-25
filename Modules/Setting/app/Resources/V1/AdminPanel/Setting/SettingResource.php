<?php

namespace Modules\Setting\app\Resources\V1\AdminPanel\Setting;

use Illuminate\Http\Resources\Json\JsonResource;

class SettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'updated_at' => $this->updated_at,
        ];
    }
}
