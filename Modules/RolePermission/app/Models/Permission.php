<?php

namespace Modules\RolePermission\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use \Spatie\Permission\Models\Permission as BasePermissionModel;

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
    public function getSubNameAttribute(): string
    {
        $arr = explode('.', $this->name);
        return end($arr);
    }

    public function getAliasAttribute(): string
    {
        return __("rolepermission::aliases.permission.$this->sub_name");
    }

    //.................Functionality.................
}
