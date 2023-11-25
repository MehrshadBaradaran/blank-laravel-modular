<?php

namespace Modules\Gallery\app\Observers;

use Illuminate\Support\Facades\Storage;
use Auth;
use Modules\Gallery\app\Models\VideoGallery;

class VideoGalleryObserver
{
    public function deleted(VideoGallery $video)
    {
        $disk = 'public';

        if (Storage::disk($disk)->exists($video->folder)) {
            Storage::disk($disk)->deleteDirectory($video->folder);
        }
    }
}
