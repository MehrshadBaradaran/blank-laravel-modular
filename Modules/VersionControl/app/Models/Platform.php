<?php

namespace Modules\VersionControl\app\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Gallery\app\Services\GalleryService;
use Modules\VersionControl\app\Enums\PlatformOSEnum;
use Modules\VersionControl\app\Observers\PlatformObserver;
use Modules\VersionControl\app\Services\PlatformService;
use Modules\VersionControl\Database\factories\PlatformFactory;

class Platform extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected static function boot(): void
    {
        parent::boot();
        self::observe(PlatformObserver::class);
    }

    protected static function newFactory(): PlatformFactory
    {
        return PlatformFactory::new();
    }

    protected static function service(): PlatformService
    {
        return new PlatformService();
    }

    //.................Casts.................
    protected $casts = [
        'os' => PlatformOSEnum::class,
        'status' => StatusEnum::class,

        'cover_paths' => 'json',
    ];

    //.................Relations.................
    public function versions(): HasMany
    {
        return $this->hasMany(Version::class);
    }

    //.................Scopes.................
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    //.................Attributes.................
    public function coverUrls(): Attribute
    {
        return Attribute::make(
            get: fn(): ?array => (new GalleryService())->getFullUrlFilesArray($this->cover_paths)
        );
    }

    public function cover(): Attribute
    {
        return Attribute::make(
            get: fn(): ?array => $this->cover_id
                ? [
                    'id' => $this->cover_id,
                    'urls' => $this->cover_urls,
                ]
                : null
        );
    }

    //.................Functionality.................

}
