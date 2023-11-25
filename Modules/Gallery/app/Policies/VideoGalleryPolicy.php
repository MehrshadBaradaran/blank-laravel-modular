<?php

namespace Modules\Gallery\app\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
use Modules\Gallery\app\Models\VideoGallery;
use Modules\User\app\Models\User;

class VideoGalleryPolicy
{
    use HandlesAuthorization;

    public function view(User $user, VideoGallery $video): Response|bool
    {
        return $video->user_id == $user->id;
    }

    public function delete(User $user, VideoGallery $video): Response|bool
    {
        return $video->user_id == $user->id;
    }
}
