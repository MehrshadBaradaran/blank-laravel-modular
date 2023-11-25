<?php

namespace Modules\RolePermission\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Guard;
use \Spatie\Permission\Models\Role as BaseRoleModel;
use Spatie\Permission\PermissionRegistrar;

class Role extends BaseRoleModel
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    //.................Casts.................
    protected $casts = [
        'visible' => 'bool',
        'types' => 'json',
    ];

    //.................Relations.................

    //.................Scopes.................
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    public function scopeHidden(Builder $query): Builder
    {
        return $query->where('visible', false);
    }

    //.................Attributes.................
    public function getPermissionsIdArrAttribute(): array
    {
        return $this->permissions
            ->map(function ($permission) {
                return $permission->id;
            })
            ->toArray();
    }

    //.................Functionality.................
}
