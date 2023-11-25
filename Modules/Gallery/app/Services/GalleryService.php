<?php

namespace Modules\Gallery\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Modules\Gallery\app\Models\GallerySetting;
use Modules\Spy\app\Utilities\SpyLogger;

class GalleryService
{
    public function getFullUrlFilesArray(?array $files): ?array
    {
        $data = [];

        if ($files) {
            foreach ($files as $name => $path) {
                $data[$name] = asset("storage/$path");
            }
        }

        return $data;
    }
}
