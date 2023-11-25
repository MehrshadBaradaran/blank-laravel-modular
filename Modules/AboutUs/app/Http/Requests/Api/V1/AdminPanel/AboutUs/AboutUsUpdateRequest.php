<?php

namespace Modules\AboutUs\app\Http\Requests\Api\V1\AdminPanel\AboutUs;

use Illuminate\Foundation\Http\FormRequest;

class AboutUsUpdateRequest extends FormRequest
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
