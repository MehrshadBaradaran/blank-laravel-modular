<?php

namespace Modules\Authentication\app\Rules;

use Illuminate\Contracts\Validation\Rule;

class Phone implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes(mixed $attribute, mixed $value): bool
    {
        return preg_match("/^989[0-9]{9}$/", $value);
    }

    public function message(): string
    {
        return __('validation.not_regex');
    }
}
