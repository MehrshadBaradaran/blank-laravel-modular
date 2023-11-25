<?php

namespace Modules\FAQ\app\Http\Requests\Api\V1\AdminPanel\FAQ;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class FAQStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question' => [
                'required', 'string', 'min:2', 'max:255',
            ],
            'answer' => [
                'required', 'string', 'min:2',
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'sort_index' => 0,

            'question' => $this->question,
            'answer' => $this->answer,

            'status' => StatusEnum::getDefaultCaseValue(),
        ];
    }
}
