<?php

namespace Modules\Authentication\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Authentication\app\Observers\VerificationTokenObserver;
use Modules\User\app\Models\User;

class VerificationToken extends Model
{
    protected $guarded = [
        'id',
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(VerificationTokenObserver::class);
    }

    //.................Casts.................
    protected $casts = [
        'is_permanent' => 'bool',
    ];

    //.................Relations.................
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //.................Scopes.................
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('expire_at', '<', now());
    }

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where('expire_at', '>', now());
    }

    public function scopePermanent(Builder $query): Builder
    {
        return $query->where('is_permanent', true)->whereNull('expire_at');
    }

    public function scopeTemporary(Builder $query): Builder
    {
        return $query->where('is_permanent', false)->whereNotNull('expire_at');
    }

    public function scopeLatestCode(Builder $query): Builder
    {
        return $query
            ->notExpired()
            ->orWhere(function ($q) {
                $q->permanent();
            })
            ->orderBy('created_at', 'desc');
    }
}
