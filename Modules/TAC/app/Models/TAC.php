<?php

namespace Modules\TAC\app\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\TAC\app\Services\TACService;

class TAC extends Model
{
    protected $table = 'terms_and_conditions';
    protected $guarded = [
        'id',
    ];

    protected static function service(): TACService
    {
        return new TACService();
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
