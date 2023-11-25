<?php

namespace Modules\VersionControl\app\Http\Requests\Api\V1\AdminPanel\Platform;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Gallery\app\Rules\ImageGalleryUploadRule;
use Modules\VersionControl\app\Enums\PlatformOSEnum;

class PlatformUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cover_id' => [
                'required', 'int',
                Rule::exists('gallery_images', 'id')
                    ->whereIn('section', ['platform', 'default',]),
                new ImageGalleryUploadRule($this->platform->cover_id),
            ],

            'title' => [
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('platforms')
                    ->where('os', $this->os)
                    ->ignore($this->platform),
            ],
            'os' => [
                'required', 'string',
                new Enum(PlatformOSEnum::class),
            ],

            'url' => [
                'required', 'url',
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'cover_id' => $this->cover_id,

            'title' => $this->title,
            'os' => $this->os,
            'url' => $this->url,
        ];
    }
}
