<?php

namespace Modules\Gallery\app\Http\Requests\Api\V1\AdminPanel\GallerySetting;

use Illuminate\Foundation\Http\FormRequest;

class GallerySettingUpdateRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'max_image_upload_size' => [
                'required', 'int', 'min:1',
            ],
            'max_video_upload_size' => [
                'required', 'int', 'min:1',
            ],


            'image_generate_patterns.*' => [
                'array', 'required_array_keys:width,height,extension,quality,observe_aspect_ratio',
            ],
            'image_generate_patterns.*.width' => [
                'int', 'min:1',
            ],
            'image_generate_patterns.*.height' => [
                'int', 'min:1',
            ],
            'image_generate_patterns.*.extension' => [
                'string', 'in:png,jpeg,jpg',
            ],
            'image_generate_patterns.*.quality' => [
                'int', 'min:0', 'max:100',
            ],
            'image_generate_patterns.*.observe_aspect_ratio' => [
                'bool',
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'max_image_upload_size' => $this->max_image_upload_size,
            'max_video_upload_size' => $this->max_video_upload_size,

            'image_generate_patterns' => $this->image_generate_patterns,
        ];
    }
}
