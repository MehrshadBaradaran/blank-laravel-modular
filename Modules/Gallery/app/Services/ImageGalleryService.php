<?php

namespace Modules\Gallery\app\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Gallery\app\Models\ImageGallery;
use Modules\Spy\app\Utilities\SpyLogger;

class ImageGalleryService
{
    protected string $permissionGroup = 'image_gallery';
    protected string $name = 'image gallery';

    public function getAlias(): string
    {
        return __('gallery::aliases.name.image-gallery');
    }

    public function create(array $data): ImageGallery
    {
        return DB::transaction(function () use ($data) {

            $image = ImageGallery::create($data);

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

    public function delete(ImageGallery $image): ?bool
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
