<?php

namespace Modules\AboutUs\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\AboutUs\app\Services\AboutUsService;

class AboutUs extends Model
{
    protected $guarded = [
        'id',
    ];

    protected static function service(): AboutUsService
    {
        return new AboutUsService();
    }

    //.................Casts.................
    protected $casts = [
        'data' => 'json',
    ];

    //.................Relations.................

    //.................Scopes.................

    //.................Attributes.................

    //.................Functionality.................

}
