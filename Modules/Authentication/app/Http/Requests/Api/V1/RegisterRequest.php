<?php

namespace Modules\Authentication\app\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Authentication\app\Rules\Phone;
use Modules\User\app\Services\UserService;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'phone' => $this->phone ? (new UserService())->formatPhoneToCode($this->phone) : null,
        ]);
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'password' => Hash::make($this->password),

            'is_registered' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'required', 'string', 'min:2', 'max:255',
            ],
            'last_name' => [
                'required', 'string', 'min:2', 'max:255',
            ],

            'phone' => [
                'required', 'string',
                new Phone(),
                Rule::exists('users')
                    ->where('is_registered', false)
                    ->whereNotNull('phone_verified_at'),
            ],

            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'max:18',
            ],
        ];
    }
}
