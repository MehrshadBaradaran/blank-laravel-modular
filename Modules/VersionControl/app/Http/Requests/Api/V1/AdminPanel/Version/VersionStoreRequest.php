<?php

namespace Modules\VersionControl\app\Http\Requests\Api\V1\AdminPanel\Version;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\VersionControl\app\Services\VersionService;

class VersionStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform_id' => [
                'required', 'int',
                Rule::exists('platforms', 'id')
                    ->where('status', true)
                    ->whereNull('deleted_at'),
            ],

            'version_number' => [
                'required', 'int', 'min:' . (new VersionService())->getLatestVersionNumber(),
                Rule::unique('versions')
                    ->where('platform_id', $this->platform_id),
            ],

            'title' => [
                'required', 'string', 'min:2', 'max:255',
            ],
            'description' => [
                'required', 'string', 'min:2',
            ],

            'force_update' => [
                'required', 'bool',
            ],
        ];
    }

    public function getSafeData(): array
    {
        return [
            'platform_id' => $this->platform_id,

            'version_number' => $this->version_number,

            'title' => $this->title,
            'description' => $this->description,

            'force_update' => $this->force_update,
        ];
    }
}
