<?php

namespace Modules\TAC\app\Resources\V1\AdminPanel\TAC;

use Illuminate\Http\Resources\Json\JsonResource;

class TACResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'updated_at' => $this->updated_at,

            'data' => $this->data,
        ];
    }
}
