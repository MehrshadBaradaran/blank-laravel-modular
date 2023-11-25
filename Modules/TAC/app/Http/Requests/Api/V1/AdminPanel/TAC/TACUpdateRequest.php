<?php

namespace Modules\TAC\app\Http\Requests\Api\V1\AdminPanel\TAC;

use Illuminate\Foundation\Http\FormRequest;

class TACUpdateRequest extends FormRequest
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
