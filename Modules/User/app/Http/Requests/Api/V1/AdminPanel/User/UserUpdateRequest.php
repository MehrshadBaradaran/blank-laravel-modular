<?php

namespace Modules\User\app\Http\Requests\Api\V1\AdminPanel\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Authentication\app\Rules\Phone;
use Modules\Gallery\app\Rules\ImageGalleryUploadRule;
use Modules\User\app\Services\UserService;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => (new UserService())->formatPhoneToCode($this->phone),
        ]);
    }

    public function rules(): array
    {
        return [
            'avatar_id' => [
                'nullable', 'numeric',
                Rule::exists('gallery_images', 'id')
                    ->whereIn('section', ['user', 'default',]),
                new ImageGalleryUploadRule($this->user->avatar_id),
            ],

            'first_name' => [
                'required', 'string', 'min:2', 'max:255',
            ],
            'last_name' => [
                'required', 'string', 'min:2', 'max:255',
            ],

            'phone' => [
                'required',
                new Phone(),
                Rule::unique('users')
                    ->ignore($this->user),
            ],


            'password' => [
                'nullable', 'string', 'confirmed', 'min:8', 'max:18',
                Rule::requiredIf(!$this->user),
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'password' => $this->password ? Hash::make($this->password) : $this->user?->password,
        ]);
    }

    public function getSafeData(): array
    {
        return [
            'avatar_id' => $this->avatar_id,

            'first_name' => $this->first_name,
            'last_name' => $this->last_name,

            'phone' => $this->phone,
            'password' => $this->password,
        ];
    }
}
