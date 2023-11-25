<?php

namespace Modules\RolePermission\app\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PermissionGroup extends Model
{
    protected $guarded = [
        'id',
    ];
    protected $with = [
        'permissions',
    ];

    //.................Casts.................
    protected $casts = [
        'visible' => 'bool',
    ];

    //.................Relations.................
    public function types(): BelongsTo
    {
        return $this->belongsTo(PermissionType::class, 'type_id');
    }

    public function permissions(): HasMany
    {
        return $this->hasMany(Permission::class, 'group_id');
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

    public function scopeControlled(Builder $query): Builder
    {
        return $query->when(!Auth::user()->is_super_admin, function ($q) {
            $q->visible();
        });
    }

    //.................Attributes.................
    public function getVisiblePermissionsAttribute(): Collection
    {
        return $this->permissions()->control()->get();
    }

    public function getAliasAttribute(): string
    {
        return __("rolepermission::alias.group.$this->name");
    }

    //.................Functionality.................
}
