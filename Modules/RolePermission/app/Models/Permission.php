<?php

namespace Modules\RolePermission\app\Models;

use Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission as BasePermissionModel;

class Permission extends BasePermissionModel
{
    protected $guarded = [
        'id',
    ];

    //.................Casts.................
    protected $casts = [
        'visible' => 'bool',
    ];

    //.................Relations.................
    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class);
    }

    //.................Scopes.................
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('visible', true);
    }

    public function scopeHidden(Builder $query): Builder
    {
        return $query->where('visible', false);
    }

    public function scopeControl(Builder $query): Builder
    {
        return $query->when(!Auth::user()->is_super_admin, function ($q) {
            $q->visible();
        });
    }

    //.................Attributes.................
    public function subName(): Attribute
    {
        return Attribute::make(
            get: fn(): string => Arr::last(explode('.', $this->name))
        );
    }

    public function alias(): Attribute
    {
        return Attribute::make(
            get: fn(): string => __("rolepermission::aliases.permission.$this->sub_name")
        );
    }

    //.................Functionality.................
}
