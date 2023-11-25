<?php

namespace Modules\TAC\app\Models;

use Illuminate\Database\Eloquent\Model;

class TAC extends Model
{
    protected $table = 'terms_and_conditions';
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
