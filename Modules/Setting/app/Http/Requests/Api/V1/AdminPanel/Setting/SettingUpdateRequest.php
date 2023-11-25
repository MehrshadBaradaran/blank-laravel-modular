<?php

namespace Modules\Setting\app\Http\Requests\Api\V1\AdminPanel\Setting;

use Illuminate\Foundation\Http\FormRequest;

class SettingUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //
        ];
    }

    public function getSafeData(): array
    {
        return [
            //
        ];
    }
}
