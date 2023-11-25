<?php

namespace Modules\Gallery\app\Observers;

use Illuminate\Support\Facades\Storage;
use Modules\Gallery\app\Models\ImageGallery;

class ImageGalleryObserver
{
    public function deleted(ImageGallery $image)
    {
        $disk = 'public';

        if (Storage::disk($disk)->exists($image->folder)) {
            Storage::disk($disk)->deleteDirectory($image->folder);
        }
    }
}
