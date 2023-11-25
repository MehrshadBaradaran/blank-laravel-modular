<?php

namespace Modules\Gallery\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Gallery\app\Enums\GallerySectionEnum;
use Modules\Gallery\app\Observers\ImageGalleryObserver;
use Modules\Gallery\app\Services\GalleryService;
use Modules\User\app\Models\User;

class ImageGallery extends Model
{
    protected $table = 'gallery_images';
    protected $guarded = [
        'id',
    ];

    protected static function boot(): void
    {
        parent::boot();
        self::observe(ImageGalleryObserver::class);
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
        if (config('gallery.image_gallery_occupation_status')) {
            $this->update([
                'occupied' => true,
            ]);
        }
    }
}
