<?php

namespace Modules\ContactUs\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\ContactUs\app\Services\ContactUsService;

class ContactUs extends Model
{
    protected $guarded = [
        'id',
    ];

    protected static function service(): ContactUsService
    {
        return new ContactUsService();
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
