<?php

namespace Modules\Gallery\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Gallery\app\Enums\GallerySectionEnum;
use Modules\Gallery\app\Observers\VideoGalleryObserver;
use Modules\Gallery\app\Services\GalleryService;
use Modules\Gallery\app\Services\VideoGalleryService;
use Modules\User\app\Models\User;

class VideoGallery extends Model
{
    protected $table = 'gallery_videos';
    protected $guarded = [
        'id',
    ];

    protected static function boot(): void
    {
        parent::boot();
        self::observe(VideoGalleryObserver::class);
    }

    protected static function service(): VideoGalleryService
    {
        return new VideoGalleryService();
    }

    //.................Casts.................
    protected $casts = [
        'section' => GallerySectionEnum::class,

        'occupied' => 'bool',

        'files' => 'json',
    ];

    //.................Relations.................
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //.................Scopes.................
    public function scopeOccupied(Builder $query): Builder
    {
        return $query->where('occupied', true);
    }

    public function scopeUnoccupied(Builder $query): Builder
    {
        return $query->where('occupied', true);
    }

    //.................Attributes.................
    public function getFilesDataAttribute(): array
    {
        return (new GalleryService())->getFullUrlFilesArray($this->files);
    }

    //.................Functionality.................
    public function occupy(): void
    {
        if (config('gallery.video_gallery_occupation_status')) {
            $this->update([
                'occupied' => true,
            ]);
        }
    }
}
