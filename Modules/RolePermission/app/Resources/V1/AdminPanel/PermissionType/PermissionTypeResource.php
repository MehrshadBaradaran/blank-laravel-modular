<?php

namespace Modules\RolePermission\app\Resources\V1\AdminPanel\PermissionType;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RolePermission\app\Resources\V1\AdminPanel\PermissionGroup\PermissionGroupCollection;

class PermissionTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'name' => $this->name,
            'alias' => $this->alias,

            'groups' => new PermissionGroupCollection($this->groups()->controlled()->get()),
        ];
    }
}
