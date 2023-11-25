<?php

namespace Modules\Banner\app\Observers;

use Modules\Gallery\app\Models\ImageGallery;
use Modules\Banner\app\Models\Banner;

class BannerObserver
{
    public function creating(Banner $banner): void
    {
        $cover = ImageGallery::find(request()->cover_id);

        $banner->cover_paths = $cover?->files;
        $cover?->occupy();
    }

    public function updating(Banner $banner): void
    {
        $cover = ImageGallery::find(request()->cover_id);

        $banner->cover_paths = $cover?->files;
        $cover?->occupy();
    }

    public function forceDeleted(Banner $banner): void
    {
        ImageGallery::whereId($banner->cover_id)?->delete();
    }
}
