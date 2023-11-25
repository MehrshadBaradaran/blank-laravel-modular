<?php

namespace Modules\Gallery\app\Http\Requests\Api\V1\App\ImageGallery;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use Modules\Gallery\app\Enums\GallerySectionEnum;
use Modules\Gallery\app\Services\ImageService;

class ImageGalleryStoreRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function passedValidation(): void
    {
        $data = (new ImageService())->handle($this->file, $this->section);

        $this->merge([
            'folder' => $data->getFolder(),
            'generated_files' => $data->getFiles(),

            'width' => $data->getWidth(),
            'height' => $data->getHeight(),
            'size' => $data->getSize(),

            'user_id' => \Auth::id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'section' => [
                'required', 'string',
                new Enum(GallerySectionEnum::class),
            ],

            'file' => [
                'required', 'mimes:jpg,jpeg,png,gif,bmp,svg,webp',
                'max:' . \Cache::get('gallerySetting')->max_image_upload_size,
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'user_id' => $this->user_id,

            'section' => $this->section,

            'folder' => $this->folder,
            'files' => $this->generated_files,

            'width' => $this->width,
            'height' => $this->height,
            'size' => $this->size,

            'occupied' => false,
        ];
    }
}
