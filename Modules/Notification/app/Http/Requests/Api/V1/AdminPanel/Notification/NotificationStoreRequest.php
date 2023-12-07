<?php

namespace Modules\Notification\app\Http\Requests\Api\V1\AdminPanel\Notification;

use App\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Modules\Notification\app\Enums\NotificationInformTypeEnum;
use Modules\Notification\app\Enums\NotificationTypeEnum;
use Modules\User\app\Models\User;

class NotificationStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function passedValidation(): void
    {
        if ($this->general) {
            $this->merge([
                'users' => User::registered()->pluck('id')->toArray(),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required', 'string', 'min:2', 'max:255',
            ],
            'body' => [
                'required', 'string', 'min:2',
            ],

            'inform_type' => [
                'required', 'string',
                new Enum(NotificationInformTypeEnum::class),
            ],

            'general' => [
                'required', 'bool',
            ],

            'users' => [
                'nullable', 'array',
                Rule::requiredIf(!$this->general),
            ],
            'users.*' => [
                'int',
                Rule::exists('users', 'id')
                    ->where('is_registered', true)
                    ->whereNull('deleted_at'),
            ],
        ];
    }

    public function safeData(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,

            'type' => NotificationTypeEnum::MANUAL,
            'status' => StatusEnum::getDefaultCaseValue(),
            'inform_type' => $this->inform_type,

            'general' => $this->general,
        ];
    }
}
