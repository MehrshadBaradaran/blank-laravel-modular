<?php

namespace Modules\User\app\Models;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Modules\Authentication\app\Models\VerificationToken;
use Modules\Gallery\app\Models\ImageGallery;
use Modules\Gallery\app\Models\VideoGallery;
use Modules\Gallery\app\Services\GalleryService;
use Modules\Notification\app\Models\Notification;
use Modules\User\app\Observers\UserObserver;
use Modules\User\app\Services\UserService;
use Modules\User\database\factories\UserFactory;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, HasRoles, HasPermissions, SoftDeletes, HasApiTokens;

    protected string $guard_name = 'web';
    protected $guarded = [
        'id',
    ];

    protected static function boot()
    {
        parent::boot();
        self::observe(UserObserver::class);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    //.................Casts.................
    protected $casts = [
        'status' => StatusEnum::class,

        'is_admin' => 'bool',
        'is_registered' => 'bool',

        'avatar_paths' => 'json',

        'last_login' => 'datetime',
        'phone_verified_at' => 'datetime',
    ];

    //.................Relations.................
    public function verificationTokens(): HasMany
    {
        return $this->hasMany(VerificationToken::class);
    }

    public function notifications(): BelongsToMany
    {
        return $this->belongsToMany(Notification::class)
            ->withPivot('read')
            ->withCasts(['read' => 'bool',]);
    }

    public function uploadedImages(): HasMany
    {
        return $this->hasMany(ImageGallery::class);
    }

    public function uploadedVideos(): HasMany
    {
        return $this->hasMany(VideoGallery::class);
    }

    //.................Scopes.................
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', false);
    }

    public function scopeRegistered(Builder $query): Builder
    {
        return $query->where('is_registered', true);
    }

    public function scopeUnregistered(Builder $query): Builder
    {
        return $query->where('is_registered', false);
    }

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('is_admin', true);
    }

    public function scopeSuperAdmin(Builder $query): Builder
    {
        return $query->whereHas('roles', function ($q) {
            $q->where('name', 'super_admin');
        });
    }

    public function scopeNotSuperAdmin(Builder $query): Builder
    {
        return $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super_admin');
        });
    }

    public function scopeWhereEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    //.................Attributes.................
    public function getAvatarUrlsAttribute(): ?array
    {
        return (new GalleryService())->getFullUrlFilesArray($this->avatar_paths);
    }

    public function getAvatarAttribute(): ?array
    {
        $data = [
            'id' => $this->avatar_id,
            'urls' => $this->avatar_urls,
        ];

        return $this->avatar_id ? $data : null;
    }

    public function getPhoneWithZeroAttribute(): string
    {
        return (new UserService())->formatPhoneToZero($this->phone);
    }

    public function getFullNameAttribute(): ?string
    {
        return ($this->first_name and $this->last_name) ? "$this->first_name $this->last_name" : null;
    }

    public function getRoleIdsArrayAttribute(): ?array
    {
        $ids = $this->roles()
            ->pluck('id')
            ->toArray();

        return !empty($ids) ? $ids : null;
    }

    public function getIsSuperAdminAttribute(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function getPermissionsArrayAttribute(): array
    {
        return $this->getAllPermissions()
            ->pluck('name')
            ->toArray();
    }

    public function getUnreadNotificationsCountAttribute(): int
    {
        return Notification::query()
            ->orWhereHas('users', function ($q) {
                $q->where('user_id', $this->id)
                    ->where('read', false);
            })
            ->count();
    }

    //.................Functionality.................
}
