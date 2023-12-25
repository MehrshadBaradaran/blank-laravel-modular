<?php

namespace Modules\VersionControl\app\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\VersionControl\app\Services\VersionService;
use Modules\VersionControl\Database\factories\VersionFactory;

class Version extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected static function newFactory(): VersionFactory
    {
        return VersionFactory::new();
    }

    protected static function service(): VersionService
    {
        return new VersionService();
    }

    //.................Casts.................
    protected $casts = [
        'force_update' => 'bool',
    ];

    //.................Relations.................
    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    //.................Scopes.................
    public function scopeForceUpdate(Builder $query): Builder
    {
        return $query->where('force_update', true);
    }

    //.................Attributes.................
    public function platformData(): Attribute
    {
        return Attribute::make(
            get: fn(): array => [
                'id' => $this->platform_id,
                'title' => $this->platform->title,
            ]
        );
    }

    //.................Functionality.................

}
