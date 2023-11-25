<?php

namespace Modules\RolePermission\app\Services;


class PermissionService
{
    public function getAlias(): string
    {
        return __('rolepermission::alias.name.permission');
    }

    public function getDefaultPermissionsArray(): array
    {
        return [
            'view',
            'create',
            'update',
            'delete',
            'change-status',
        ];
    }
}
