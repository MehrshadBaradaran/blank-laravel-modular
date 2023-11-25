<?php

namespace Modules\Spy\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\RolePermission\app\Models\Permission;
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
    public function getPermissionDataArrayAttribute(): array|null
    {
        $data = [
            'id' => $this->permission_id,
            'name' => $this->permission?->name,
            'alias' => $this->permission?->alias,
        ];
        return $this->permission_id ? $data : null;
    }

    public function getTargetDataArrayAttribute(): array|null
    {
        $data = [
            'id' => $this->target_id,
            'name' => class_basename($this->target_type),
            'alias' => $this->target_type ? $this->target_type::service()->getAlias() : null,
        ];

        return $this->target_id ? $data : null;
    }

    //.................Functionality.................

}
