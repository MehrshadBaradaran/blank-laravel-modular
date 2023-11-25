<?php

namespace Modules\Gallery\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Gallery\app\Models\VideoGallery;
use Modules\Spy\app\Utilities\SpyLogger;

class VideoGalleryService
{
    protected string $permissionGroup = 'video_gallery';
    protected string $name = 'video gallery';

    public function getAlias(): string
    {
        return __('gallery::aliases.name.video-gallery');
    }

    public function create(array $data): VideoGallery
    {
        return DB::transaction(function () use ($data) {

            $image = VideoGallery::create($data);

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("create $this->name")
                ->target($image)
                ->permissionName("admin_panel.$this->permissionGroup.create")
                ->action('create')
                ->submit();

            return $image;
        });
    }

    public function delete(VideoGallery $image): ?bool
    {
        return DB::transaction(function () use ($image) {

            $result = $image->delete();

            (new SpyLogger())
                ->userId(Auth::id())
                ->title("delete $this->name")
                ->target($image)
                ->permissionName("admin_panel.$this->permissionGroup.delete")
                ->action('delete')
                ->submit();

            return $result;
        });
    }
}
