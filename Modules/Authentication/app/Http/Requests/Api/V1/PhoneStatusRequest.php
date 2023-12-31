<?php

namespace Modules\Authentication\app\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Authentication\app\Rules\Phone;
use Modules\User\app\Services\UserService;

class PhoneStatusRequest extends FormRequest
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
        return [
            'phone' => [
                'required', 'string',
                new Phone(),
            ],
        ];
    }
}
