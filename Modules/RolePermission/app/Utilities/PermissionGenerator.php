<?php

namespace Modules\RolePermission\app\Utilities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Modules\RolePermission\app\Models\PermissionGroup;
use Modules\RolePermission\app\Models\PermissionType;
use Exception;
use Modules\RolePermission\app\Services\PermissionService;

class PermissionGenerator
{
    protected PermissionType $type;
    protected PermissionGroup $group;
    protected array $defaultPermissions;
    protected array $permissions;

    public function __construct(string $type)
    {
        $this->type = PermissionType::whereName($type)->firstOrFail();
        $this->defaultPermissions = (new PermissionService())->getDefaultPermissionsArray();
        $this->permissions = [];
    }

    protected function generatePermissionName(string $name): string
    {
        if (!isset($this->group)) {
            throw new Exception('No permission group provided');
        }

        return implode('.', [$this->type->name, $this->group->name, $name]);
    }

    protected function getPushedPermissionNamesArray(): array
    {
        return array_map(function ($permission) {
            return $permission['name'];
        }, $this->permissions);
    }

    protected function pushToPermissions(string $name, bool $visible = true): void
    {
        $permissionName = $this->generatePermissionName($name);

        if (!in_array($permissionName, $this->getPushedPermissionNamesArray())) {
            array_push($this->permissions, [
                'group_id' => $this->group->id,
                'name' => $permissionName,
                'visible' => $visible,
                'guard_name' => 'web',
            ]);
        }
    }

    public function new(string $modelOrGroupName, bool $visible = true): self
    {
        $name = is_subclass_of($modelOrGroupName, Model::class)
            ? Str::snake(class_basename($modelOrGroupName))
            : $modelOrGroupName;

        $group = PermissionGroup::firstOrCreate(
            [
                'type_id' => $this->type->id,
                'name' => $name,
            ],
            [
                'type_id' => $this->type->id,
                'name' => $name,
                'visible' => $visible,
            ]);

        $this->group = $group;

        return $this;
    }

    public function all(bool $visible = true): self
    {
        foreach ($this->defaultPermissions as $name) {
            $this->pushToPermissions($name, $visible);
        }

        return $this;
    }

    public function view(bool $visible = true): self
    {
        $this->pushToPermissions(str(__FUNCTION__)->snake(), $visible);

        return $this;
    }

    public function create(bool $visible = true): self
    {
        $this->pushToPermissions(str(__FUNCTION__)->snake(), $visible);

        return $this;
    }

    public function update(bool $visible = true): self
    {
        $this->pushToPermissions(str(__FUNCTION__)->snake(), $visible);

        return $this;
    }

    public function delete(bool $visible = true): self
    {
        $this->pushToPermissions(str(__FUNCTION__)->snake(), $visible);

        return $this;
    }

    public function changeStatus(bool $visible = true): self
    {
        $this->pushToPermissions(str(__FUNCTION__)->snake(), $visible);

        return $this;
    }

    public function attach(bool $visible = true): self
    {
        $this->pushToPermissions(str(__FUNCTION__)->snake(), $visible);

        return $this;
    }

    public function sort(bool $visible = true): self
    {
        $this->pushToPermissions(str(__FUNCTION__)->snake(), $visible);

        return $this;
    }

    public function extra(string $name, bool $visible = true): self
    {
        $this->pushToPermissions($name, $visible);

        return $this;
    }

    public function except(string|array $names): self
    {
        $names = is_array($names) ? $names : [$names];

        foreach ($this->permissions as $key => $permission) {
            foreach ($names as $name) {
                if ($permission['name'] == $this->generatePermissionName($name)) {
                    unset($this->permissions[$key]);
                }
            }
        }

        return $this;
    }

    public function get(): array
    {
        return $this->permissions;
    }
}
