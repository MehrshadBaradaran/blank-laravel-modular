<?php

namespace Modules\RolePermission\app\Services;

use Modules\RolePermission\app\Models\PermissionType;

class PermissionTypeService
{
    protected array $types;

    public function __construct()
    {
        $this->types = [
            'admin_panel',
        ];
    }

    public function getAlias(): string
    {
        return __('rolepermission::alias.name.permission-type');
    }

    public function getAdminTypeName(): string
    {
        return $this->types[0];
    }

    public function getTypesArr(): array
    {
        return $this->types;
    }

    public function getTypeInitialData(): array
    {
        $data = [];
        $date = now();

        foreach ($this->getTypesArr() as $type) {

            array_push($data, [
                'name' => $type,
                'created_at' => $date,
            ]);
        }

        return $data;
    }

    public function getTypesDataArrayByPermissions(array $permissions): array
    {
        return PermissionType::query()
            ->whereHas('permissions', function ($q) use ($permissions) {
                $q->whereIn('permissions.id', $permissions);
            })
            ->without('groups')
            ->pluck('name')
            ->toArray();
    }
}
