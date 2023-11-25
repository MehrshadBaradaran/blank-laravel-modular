<?php

namespace Modules\Gallery\app\Resources\V1\AdminPanel\ImageGallery;

use Illuminate\Http\Resources\Json\JsonResource;

class ImageGalleryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'section' => $this->section->value,

            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->size,

            'occupied' => $this->occupied,

            'files' => $this->files_data,
        ];
    }
}
