<?php

namespace Modules\Authentication\app\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Modules\Authentication\app\Rules\Phone;
use Modules\Authentication\app\Services\VerificationTokenService;
use Modules\User\app\Services\UserService;

class PassResetRequest extends FormRequest
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
        ]);
    }

    public function rules(): array
    {
        $otp = (new VerificationTokenService())->getLatestOTPByPhone($this->phone);

        return [
            'phone' => [
                'required', 'string',
                new Phone(),
                Rule::exists('users')
                    ->where('is_registered', true)
                    ->where('status', true)
                    ->whereNotNull('phone_verified_at'),
            ],

            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'max:18',
            ],

            'code' => [
                'required', 'numeric', "in:$otp",
            ],
        ];
    }
}
