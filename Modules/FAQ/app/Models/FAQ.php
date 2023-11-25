<?php

namespace Modules\FAQ\app\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\FAQ\Database\factories\FAQFactory;

class FAQ extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'frequently_asked_questions';
    protected $guarded = [
        'id',
    ];

    protected static function newFactory(): FAQFactory
    {
        return FAQFactory::new();
    }

    //.................Casts.................
    protected $casts = [
        'status' => StatusEnum::class,
    ];

    //.................Relations.................

    //.................Scopes.................
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    //.................Attributes.................

    //.................Functionality.................

}
