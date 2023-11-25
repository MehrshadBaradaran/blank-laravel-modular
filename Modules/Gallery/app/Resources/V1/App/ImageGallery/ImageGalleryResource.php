<?php

namespace Modules\Gallery\app\Resources\V1\App\ImageGallery;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageGalleryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'section' => $this->section->value,
            'extension' => $this->extension,

            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->height,

            'occupied' => $this->occupied,

            'files' => $this->files_data,
        ];
    }
}
