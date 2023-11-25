<?php

namespace Modules\User\app\Http\Requests\Api\V1\App\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\RolePermission\app\Models\PermissionType;
use Modules\RolePermission\app\Models\Role;
use Modules\RolePermission\app\Services\PermissionTypeService;

class PasswordUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'old_password' => [
                'required', 'string', 'current_password:api',
            ],

            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'max:18',
            ],

        ];
    }

    protected function passedValidation(): void
    {
        $this->merge([
            'password' => \Hash::make($this->password),
        ]);
    }

    public function getSafeData(): array
    {
        return [
            'password' => $this->password
        ];
    }
}
