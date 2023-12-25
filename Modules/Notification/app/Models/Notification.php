<?php

namespace Modules\Notification\app\Models;

use App\Enums\StatusEnum;
use Auth;
use DB;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Notification\app\Enums\NotificationInformTypeEnum;
use Modules\Notification\app\Enums\NotificationTypeEnum;
use Modules\Notification\app\Services\NotificationService;
use Modules\Notification\Database\factories\NotificationFactory;
use Modules\User\app\Models\User;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [
        'id',
    ];

    protected static function newFactory(): NotificationFactory
    {
        return NotificationFactory::new();
    }

    protected static function service(): NotificationService
    {
        return new NotificationService();
    }

    //.................Casts.................
    protected $casts = [
        'type' => NotificationTypeEnum::class,
        'inform_type' => NotificationInformTypeEnum::class,
        'status' => StatusEnum::class,

        'general' => 'bool',
    ];

    //.................Relations.................
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withPivot('read')
            ->withCasts(['read' => 'bool',]);
    }

    //.................Scopes.................
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', StatusEnum::TRUE);
    }

    public function scopeGeneral(Builder $query): Builder
    {
        return $query->where('general', StatusEnum::FALSE);
    }

    //.................Attributes.................
    public function userIds(): Attribute
    {
        return Attribute::make(
            get: fn(): ?array => $this->users()->exists() ? $this->users()->pluck('users.id')->toArray() : null
        );
    }

    public function usersData(): Attribute
    {
        return Attribute::make(
            get: function (): ?array {
                return !$this->general
                    ? $this->users()
                        ->get(['users.id', 'users.first_name', 'users.last_name',])
                        ->map(function (User $user) {
                            return [
                                'id' => $user->id,
                                'full_name' => $user->full_name,
                            ];
                        })
                        ->toArray()
                    : null;

            }
        );
    }

    public function isRead(): Attribute
    {
        return Attribute::make(
            get: fn(): bool => (bool)DB::table('notification_user')
                ->where('user_id', Auth::id())
                ->where('notification_id', $this->id)
                ->first()
                ?->read
        );
    }

    //.................Functionality.................
    public function markAsRead(): void
    {
        $this->users()->syncWithPivotValues([Auth::id(),], ['read' => true,]);
    }
}
