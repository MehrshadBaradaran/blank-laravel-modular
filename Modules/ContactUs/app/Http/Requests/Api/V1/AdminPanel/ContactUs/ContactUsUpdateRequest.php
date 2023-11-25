<?php

namespace Modules\ContactUs\app\Http\Requests\Api\V1\AdminPanel\ContactUs;

use Illuminate\Foundation\Http\FormRequest;

class ContactUsUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => [
                'required',
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
