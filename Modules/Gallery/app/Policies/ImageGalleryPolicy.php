<?php

namespace Modules\Gallery\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Gallery\app\Models\ImageGallery;
use Modules\User\app\Models\User;

class ImageGalleryPolicy
{
    use HandlesAuthorization;

    public function view(User $user, ImageGallery $image): Response|bool
    {
        return $image->user_id == $user->id;
    }

    public function delete(User $user, ImageGallery $image): Response|bool
    {
        return $image->user_id == $user->id;
    }
}
