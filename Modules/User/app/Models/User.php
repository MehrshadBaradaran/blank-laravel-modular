<?php /** @noinspection PhpUnused */

namespace Modules\User\app\Models;

use App\Enums\StatusEnum;
use Hash;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function boot(): void
    {
        parent::boot();
        self::observe(UserObserver::class);
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected static function service(): UserService
    {
        return new UserService();
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
    public function avatarUrls(): Attribute
    {
        return Attribute::make(
            get: fn(): ?array => (new GalleryService())->getFullUrlFilesArray($this->avatar_paths)
        );
    }

    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn(): ?array => $this->avatar_id
                ? [
                    'id' => $this->avatar_id,
                    'urls' => $this->avatar_urls,
                ]
                : null
        );
    }

    public function phoneWithZero(): Attribute
    {
        return Attribute::make(
            get: fn(): string => (new UserService())->formatPhoneToZero($this->phone)
        );
    }

    public function fullName(): Attribute
    {
        return Attribute::make(
            get: fn(): string => ($this->first_name and $this->last_name) ? "$this->first_name $this->last_name" : null
        );
    }

    public function isSuperAdmin(): Attribute
    {
        return Attribute::make(
            get: fn(): bool => $this->hasRole('super_admin')
        );
    }

    public function roleIdsArray(): Attribute
    {
        return Attribute::make(
            get: fn(): ?array => $this->roles()->exists() ? $this->roles()->pluck('id')->toArray() : null
        );
    }

    public function permissionsArray(): Attribute
    {
        return Attribute::make(
            get: fn(): array => $this->getAllPermissions()->pluck('name')->toArray()
        );
    }

    public function unreadNotificationsCount(): Attribute
    {
        return Attribute::make(
            get: fn(): int => Notification::query()
                ->whereHas('users', function ($q) {
                    $q->where('user_id', $this->id)
                        ->where('read', false);
                })
                ->count()
        );
    }

    public function password(): Attribute
    {
        return Attribute::make(
            set: fn($value) => $this->attributes['password'] = Hash::make($value)
        );
    }

    //.................Functionality.................
}
