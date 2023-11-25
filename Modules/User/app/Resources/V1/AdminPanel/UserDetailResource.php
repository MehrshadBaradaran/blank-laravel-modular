<?php

namespace Modules\User\app\Resources\V1\AdminPanel;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,

            'phone' => $this->phone_with_zero,

            'is_admin' => $this->is_admin,
            'status' => $this->status->getBoolValue(),

            'last_login' => $this->last_login,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'avatar' => $this->avatar,
            'roles' => $this->role_ids_array,
        ];
    }
}
