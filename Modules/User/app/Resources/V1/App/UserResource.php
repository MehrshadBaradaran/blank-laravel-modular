<?php

namespace Modules\User\app\Resources\V1\App;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,

            'phone' => $this->phone_with_zero,

            'avatar' => $this->avatar,
        ];
    }
}
