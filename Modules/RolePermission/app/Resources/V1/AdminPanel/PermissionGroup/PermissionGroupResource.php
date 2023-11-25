<?php

namespace Modules\RolePermission\app\Resources\V1\AdminPanel\PermissionGroup;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\RolePermission\app\Resources\V1\AdminPanel\Permission\PermissionCollection;

class PermissionGroupResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'alias' => $this->alias,
            'permissions' => new PermissionCollection($this->visible_permissions),
        ];
    }
}
