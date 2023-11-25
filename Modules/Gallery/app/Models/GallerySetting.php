<?php

namespace Modules\Gallery\app\Models;

use Illuminate\Database\Eloquent\Model;

class GallerySetting extends Model
{
    protected $guarded = [
        'id',
    ];

    //.................Casts.................
    protected $casts = [
        'image_generate_patterns' => 'json',
    ];

    //.................Relations.................

    //.................Scopes.................

    //.................Attributes.................

    //.................Functionality.................

}
