<?php

namespace Modules\Spy\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RolePermission\app\Models\Permission;
use Modules\RolePermission\app\Resources\V1\AdminPanel\Permission\PermissionResource;
use Modules\Spy\app\Enums\SpyActionEnum;
use \Modules\Spy\Database\factories\SpyFactory;
use Modules\User\app\Models\User;

class Spy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'spied_logs';
    protected $guarded = [
        'id',
    ];

    protected static function newFactory(): SpyFactory
    {
        return SpyFactory::new();
    }

    //.................Casts.................
    protected $casts = [
        'action' => SpyActionEnum::class,

        'request_data' => 'json',
        'request_device_data' => 'json',
        'user_data' => 'json',
        'target_data' => 'json',
    ];

    //.................Relations.................
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function target(): MorphTo
    {
        return $this->morphTo('target');
    }

    //.................Scopes.................
    public function scopeWhereAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    //.................Attributes.................
    public function permissionData(): Attribute
    {
        return Attribute::make(
            get: fn(): ?PermissionResource => $this->permission_id ? PermissionResource::make($this->permission) : null
        );
    }

    public function targetInfo(): Attribute
    {
        return Attribute::make(
            get: fn(): ?array => $this->target_id
                ? [
                    'id' => $this->target_id,
                    'name' => class_basename($this->target_type),
                    'alias' => $this->target_type ? $this->target_type::service()->getAlias() : null,
                ]
                : null
        );
    }

    //.................Functionality.................

}
