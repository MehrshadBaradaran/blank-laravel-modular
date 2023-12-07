<?php

namespace Modules\Setting\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Setting\app\Services\SettingService;

class Setting extends Model
{

    protected $guarded = [
        'id',
    ];

    protected static function service(): SettingService
    {
        return new SettingService();
    }

    //.................Casts.................
    protected $casts = [
        //
    ];

    //.................Relations.................

    //.................Scopes.................

    //.................Attributes.................

    //.................Functionality.................

}
