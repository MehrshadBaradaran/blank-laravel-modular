<?php

namespace Modules\User\app\Http\Requests\Api\V1\AdminPanel\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\RolePermission\app\Models\PermissionType;
use Modules\RolePermission\app\Models\Role;
use Modules\RolePermission\app\Services\PermissionTypeService;

class ChangeUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => [
                'nullable', 'array',
            ],
            'roles.*' => [
                'int',
                Rule::exists('roles', 'id')
                    ->where('visible', true)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
        ]);
    }

    public function getSafeData(): array
    {
        return [
            'is_admin' => $this->is_admin
        ];
    }
}
