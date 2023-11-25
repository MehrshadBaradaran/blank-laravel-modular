<?php

namespace Modules\AboutUs\app\Models;

use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    protected $guarded = [
        'id',
    ];

    //.................Casts.................
    protected $casts = [
        'data' => 'json',
    ];

    //.................Relations.................

    //.................Scopes.................

    //.................Attributes.................

    //.................Functionality.................

}
