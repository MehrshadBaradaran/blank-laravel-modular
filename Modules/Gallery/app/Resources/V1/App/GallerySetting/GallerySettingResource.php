<?php

namespace Modules\Gallery\app\Resources\V1\App\GallerySetting;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Gallery\app\Enums\ImageGallerySectionEnum;

class GallerySettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'max_image_upload_size' => $this->max_image_upload_size,
            'max_video_upload_size' => $this->max_video_upload_size,

            'sections' => ImageGallerySectionEnum::getValues(),
        ];
    }
}
