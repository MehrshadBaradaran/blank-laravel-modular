<?php

namespace Modules\RolePermission\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\app\Models\User;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function checkPermission(User $user, string $permission): bool
    {
        return $user->can($permission);
    }
}
