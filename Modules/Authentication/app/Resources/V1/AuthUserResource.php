<?php

namespace Modules\Authentication\app\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
{
    public function toArray($request): array
    {
        $permissions = collect($this->getAllPermissions())->map(function ($permission) {
            return $permission->name;
        });

        return [
            'id' => $this->id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,

            'phone' => $this->phone_with_zero,
            'unread_notifications_count' => $this->unread_notifications_count,

            'is_admin' => $this->is_admin,

            'avatar' => $this->avatar,
            'roles' => $this->role_ids_array,
            'permissions' => $permissions->toArray(),
        ];
    }
}
