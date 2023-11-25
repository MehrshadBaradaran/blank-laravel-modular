<?php

namespace Modules\RolePermission\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\RolePermission\app\Models\Role;
use Modules\Spy\app\Utilities\SpyLogger;

class RoleService
{
    protected string $permissionGroup = 'role';
    protected string $name = 'role';

    public function getAlias(): string
    {
        return __('rolepermission::aliases.name.role');
    }

    public function create(array $data, array $permissions): Role
    {
            return DB::transaction(function () use ($data, $permissions) {
            $role = Role::create($data);

            $role->permissions()->attach($permissions);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($role)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

                return $role;
            });
    }

    public function update(Role $role, array $data, array $permissions): Role
    {
        DB::transaction(function () use ($role, $data, $permissions) {
            $role->update($data);

            $role->permissions()->sync($permissions);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("update $this->name")
                ->target($role)
                ->permissionName("admin_panel.$this->permissionGroup.update")
                ->action('update')
                ->submit();
        });

        return $role;
    }

    public function delete(Role $role): bool
    {
        return DB::transaction(function () use ($role) {

            $result = $role->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($role)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }
}
