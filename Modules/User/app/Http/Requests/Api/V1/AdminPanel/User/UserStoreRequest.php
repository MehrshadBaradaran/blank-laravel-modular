<?php

namespace Modules\User\app\Http\Requests\Api\V1\AdminPanel\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Authentication\app\Rules\Phone;
use Modules\Gallery\app\Rules\ImageGalleryUploadRule;
use Modules\User\app\Services\UserService;

class UserStoreRequest extends FormRequest
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
                new ImageGalleryUploadRule($this->user?->avatar_id),
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
            ],


            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'max:18',
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'phone_verified_at' => now(),
            'password' => Hash::make($this->password),

            'is_registered' => true,
            'is_admin' => false,
            'status' => true,
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

            'phone_verified_at' => $this->phone_verified_at,

            'is_registered' => $this->is_registered,
            'is_admin' => $this->is_admin,
            'status' => $this->status,
        ];
    }
}
