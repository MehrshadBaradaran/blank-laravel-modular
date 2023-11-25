<?php

namespace Modules\RolePermission\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\RolePermission\app\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    public function checkVisibility(Role $role, string $permission): bool
    {
        return $role->visible;
    }
}
