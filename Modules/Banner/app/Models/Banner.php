<?php

namespace Modules\Banner\app\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Banner\app\Observers\BannerObserver;
use Modules\Banner\app\Services\BannerService;
use \Modules\Banner\Database\factories\BannerFactory;
use Modules\Gallery\app\Services\GalleryService;

class Banner extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(BannerObserver::class);
    }

    protected static function newFactory(): BannerFactory
    {
        return BannerFactory::new();
    }

    protected static function service(): BannerService
    {
        return new BannerService();
    }

    //.................Casts.................
    protected $casts = [
        'status' => StatusEnum::class,

        'cover_paths' => 'json',
    ];

    //.................Relations.................

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
    public function getCoverUrlsAttribute(): ?array
    {
        return (new GalleryService())->getFullUrlFilesArray($this->cover_paths);
    }

    public function getCoverAttribute(): ?array
    {
        $data = [
            'id' => $this->cover_id,
            'urls' => $this->cover_urls,
        ];

        return $this->cover_id ? $data : null;
    }

    //.................Functionality.................

}
