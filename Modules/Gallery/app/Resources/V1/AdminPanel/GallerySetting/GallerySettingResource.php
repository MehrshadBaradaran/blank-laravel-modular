<?php

namespace Modules\Gallery\app\Resources\V1\AdminPanel\GallerySetting;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Gallery\app\Enums\ImageGallerySectionEnum;

class GallerySettingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'max_image_upload_size' => $this->max_image_upload_size,
            'max_video_upload_size' => $this->max_video_upload_size,

            'updated_at' => $this->updated_at,

            'image_generate_patterns' => $this->image_generate_patterns,

            'sections' => ImageGallerySectionEnum::getValues(),
        ];
    }
}
