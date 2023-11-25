<?php

namespace Modules\Banner\app\Http\Requests\Api\V1\AdminPanel\Banner;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Gallery\app\Rules\ImageGalleryUploadRule;

class BannerStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required', 'string', 'min:2', 'max:225',
            ],

            'url' => [
                'nullable', 'url',
            ],

            'cover_id' => [
                'required', 'numeric',
                Rule::exists('gallery_images', 'id')
                    ->whereIn('section', ['banner', 'default',]),
                new ImageGalleryUploadRule($this->banner?->cover_id),
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'cover_id' => $this->cover_id,

            'title' => $this->title,
            'url' => $this->url,

            'status' => true,
        ];
    }
}
