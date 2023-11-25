<?php

namespace Modules\FAQ\app\Http\Requests\Api\V1\AdminPanel\FAQ;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FAQSortRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => [
                'required', 'array',
            ],
            'ids.*' => [
                'int', 'distinct',
                Rule::exists('frequently_asked_questions', 'id')
                    ->where('status', true)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'ids' => $this->ids,
        ];
    }
}
