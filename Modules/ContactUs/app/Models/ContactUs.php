<?php

namespace Modules\ContactUs\app\Models;

use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
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
