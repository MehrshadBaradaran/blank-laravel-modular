<?php

namespace Modules\RolePermission\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RolePermission\app\Services\RoleService;
use Spatie\Permission\Guard;
use \Spatie\Permission\Models\Role as BaseRoleModel;
use Spatie\Permission\PermissionRegistrar;

class Role extends BaseRoleModel
{
    use SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected static function service(): RoleService
    {
        return new RoleService();
    }

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
    public function permissionIds(): Attribute
    {
        return Attribute::make(
            get: fn(): array => $this->getAllPermissions()->pluck('id')->toArray()
        );
    }

    //.................Functionality.................
}
