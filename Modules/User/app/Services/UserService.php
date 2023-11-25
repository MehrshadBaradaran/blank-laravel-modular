<?php

namespace Modules\User\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\RolePermission\app\Models\Role;
use Modules\RolePermission\app\Services\PermissionTypeService;
use Modules\Spy\app\Utilities\SpyLogger;
use Modules\User\app\Models\User;

class UserService
{
    protected string $permissionGroup = 'user';
    protected string $name = 'user';

    public function getAlias(): string
    {
        return __('user::alias.name.user');
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($user)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

            return $user;
        });
    }

    public function update(User $user, array $data): User
    {
        DB::transaction(function () use ($user, $data) {
            $user->update($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($user)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();
        });

        return $user;
    }

    public function delete(User $user): bool
    {
        return DB::transaction(function () use ($user) {

            $result = $user->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($user)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }

    public function changeStatus(User $user, mixed $status): User
    {
        return DB::transaction(function () use ($user, $status) {

            User::withoutEvents(function () use ($user, $status) {
                $user->update([
                    'status' => $status,
                ]);
            });

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("change $this->name status")
                ->description("change $this->name status to {$user->status->getText()}")
                ->target($user)
                ->permissionName("admin_panel.$this->permissionGroup.change-status")
                ->action('status')
                ->submit();

            return $user;
        });
    }

    public function changeRole(User $user, array $roles): User
    {
        return DB::transaction(function () use ($user, $roles) {

            User::withoutEvents(function () use ($user) {
                $user->update([
                    'is_admin' => $this->getIsAdminByRoles(),
                ]);
            });

            $user->roles()->sync($roles);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("change $this->name role")
                ->target($user)
                ->permissionName('admin_panel.role.attach')
                ->submit();

            return $user;
        });
    }

    public function updatePassword(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {

            User::withoutEvents(function () use ($user, $data) {
                $user->update($data);
            });

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name password")
                ->target($user)
                ->submit();

            return $user;
        });
    }

    public function getIsAdminByRoles(array $roleIds = []): bool
    {
        return Role::query()
            ->whereIn('id', $roleIds)
            ->whereJsonContains('types', (new PermissionTypeService())->getAdminTypeName())
            ->exists();
    }

    public function formatPhoneToCode(string $phone, string $code = '98'): string
    {
        return preg_replace('/^0/', $code, $phone);
    }

    public function formatPhoneToZero(string $phone, string $code = '98'): string
    {
        return preg_replace("/^$code/", '0', $phone);
    }
}
