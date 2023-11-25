<?php

namespace Modules\User\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\User\app\Models\User;

class UserPolicy
{
    use HandlesAuthorization;

    public function view(User $user, User $model): bool
    {
        return ($user->id == $model->id) or !$model->is_super_admin;// Only super admin can make changes on super admin
    }

    public function update(User $user, User $model): bool
    {
        return ($user->id == $model->id) or !$model->is_super_admin;
    }

    public function changeStatus(User $user, User $model): bool
    {
        return ($user->id == $model->id) or !$model->is_super_admin;
    }

    public function changeRole(User $user, User $model): bool
    {
        return ($user->id == $model->id) or !$model->is_super_admin;
    }

    public function delete(User $user, User $model): bool
    {
        return ($user->id == $model->id) or !$model->is_super_admin;
    }

    public function forceDelete(User $user, User $model): bool
    {
        return ($user->id == $model->id) or !$model->is_super_admin;
    }
}
