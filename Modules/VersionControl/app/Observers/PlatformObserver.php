<?php

namespace Modules\VersionControl\app\Observers;

use Modules\Gallery\app\Models\ImageGallery;
use Modules\VersionControl\app\Models\Platform;

class PlatformObserver
{
    public function creating(Platform $platform): void
    {
        $cover = ImageGallery::find(request()->cover_id);

        $platform->cover_paths = $cover?->files;
        $cover?->occupy();
    }

    public function updating(Platform $platform): void
    {
        $cover = ImageGallery::find(request()->cover_id);

        $platform->cover_paths = $cover?->files;
        $cover?->occupy();
    }

    public function forceDeleted(Platform $platform): void
    {
        ImageGallery::whereId($platform->cover_id)->delete();
    }
}
