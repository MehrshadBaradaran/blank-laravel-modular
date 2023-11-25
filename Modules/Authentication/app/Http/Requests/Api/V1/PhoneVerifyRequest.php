<?php

namespace Modules\Authentication\app\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\Authentication\app\Rules\Phone;
use Modules\Authentication\app\Services\VerificationTokenService;
use Modules\User\app\Services\UserService;

class PhoneVerifyRequest extends FormRequest
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

    public function rules(): array
    {
        $otp = (new VerificationTokenService())->getLatestOTPByPhone($this->phone);

        return [
            'phone' => [
                'required', 'string',
                new Phone(),
                Rule::exists('users', 'phone'),
            ],

            'code' => [
                'required', 'numeric', "in:$otp",
            ],
        ];
    }
}
