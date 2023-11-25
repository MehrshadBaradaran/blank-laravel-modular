<?php

namespace Modules\User\app\Observers;

use Modules\Gallery\app\Models\ImageGallery;
use Modules\User\app\Models\User;

class UserObserver
{
    public function creating(User $user): void
    {
        $avatar = ImageGallery::find(request()->avatar_id);

        $user->avatar_paths = $avatar?->files;
        $avatar?->occupy();
    }

    public function updating(User $user): void
    {
        $avatar = ImageGallery::find(request()->avatar_id);

        $user->avatar_paths = $avatar?->files;
        $avatar?->occupy();
    }

    public function forceDeleted(User $user): void
    {
        ImageGallery::whereId($user->avatar_id)?->delete();
    }
}
