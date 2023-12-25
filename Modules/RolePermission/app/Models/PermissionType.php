<?php

namespace Modules\RolePermission\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class PermissionType extends Model
{
    protected $guarded = ['id',];
    protected $with = ['groups',];

    //.................Casts.................
    protected $casts = [
        //
    ];

    //.................Relations.................
    public function groups(): HasMany
    {
        return $this->hasMany(PermissionGroup::class, 'type_id');
    }

    public function permissions(): HasManyThrough
    {
        return $this->hasManyThrough(
            Permission::class,
            PermissionGroup::class,
            'type_id',
            'group_id'
        );
    }

    //.................Scopes.................
    public function scopeWhereName(Builder $query, string $name): Builder
    {
        return $query->where('name', $name);
    }

    //.................Attributes.................
    public function alias(): Attribute
    {
        return Attribute::make(
            get: fn(): string => __("rolepermission::aliases.type.$this->name")
        );
    }

    //.................Functionality.................
}
