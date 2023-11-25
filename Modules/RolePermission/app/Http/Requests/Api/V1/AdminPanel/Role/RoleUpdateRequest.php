<?php

namespace Modules\RolePermission\app\Http\Requests\Api\V1\AdminPanel\Role;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\RolePermission\app\Models\PermissionType;

class RoleUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'guard_name' => 'web',
            'types' => PermissionType::getTypeArrByPermissions($this->permissions),
            'visible' => true,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'min:2', 'max:255',
                Rule::unique('roles')
                    ->where('guard_name', 'web')
                    ->whereNull('deleted_at')
                    ->ignore($this->role),
            ],

            'permissions' => [
                'required', 'array'
            ],
            'permissions.*' => [
                'int',
                Rule::exists('permissions', 'id')
                    ->where('visible', true),
            ],
        ];
    }

    public function safeData(): array
    {
        return [
            'name' => $this->name,
            'guard_name' => $this->guard_name,
            'types' => $this->types,
            'visible' => $this->visible,
        ];
    }
}
