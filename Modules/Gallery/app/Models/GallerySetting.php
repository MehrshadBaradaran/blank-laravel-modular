<?php

namespace Modules\Gallery\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Gallery\app\Services\GallerySettingService;

class GallerySetting extends Model
{
    protected $guarded = [
        'id',
    ];

    protected static function service(): GallerySettingService
    {
        return new GallerySettingService();
    }

    //.................Casts.................
    protected $casts = [
        'image_generate_patterns' => 'json',
    ];

    //.................Relations.................

    //.................Scopes.................

    //.................Attributes.................

    //.................Functionality.................

}
