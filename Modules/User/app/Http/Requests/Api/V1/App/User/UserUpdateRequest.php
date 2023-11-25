<?php

namespace Modules\User\app\Http\Requests\Api\V1\App\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Gallery\app\Rules\ImageGalleryUploadRule;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar_id' => [
                'nullable', 'numeric',
                Rule::exists('gallery_images', 'id')
                    ->whereIn('section', ['user', 'default',]),
                new ImageGalleryUploadRule($this->user?->avatar_id),
            ],

            'first_name' => [
                'required', 'string', 'min:2', 'max:255',
            ],
            'last_name' => [
                'required', 'string', 'min:2', 'max:255',
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'avatar_id' => $this->avatar_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
        ];
    }
}
