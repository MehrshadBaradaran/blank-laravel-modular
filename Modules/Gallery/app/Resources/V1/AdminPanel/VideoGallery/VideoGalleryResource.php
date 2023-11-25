<?php

namespace Modules\Gallery\app\Resources\V1\AdminPanel\VideoGallery;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoGalleryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'section' => $this->section->value,

            'duration' => $this->duration,
            'size' => $this->size,

            'occupied' => $this->occupied,

            'files' => $this->files_data,
        ];
    }
}
