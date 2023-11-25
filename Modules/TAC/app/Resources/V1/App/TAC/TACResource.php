<?php

namespace Modules\TAC\app\Resources\V1\App\TAC;

use Illuminate\Http\Resources\Json\JsonResource;

class TACResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
