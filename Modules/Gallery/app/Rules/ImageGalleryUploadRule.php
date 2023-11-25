<?php

namespace Modules\Gallery\app\Rules;

use Illuminate\Contracts\Validation\Rule;
use Modules\Gallery\app\Models\ImageGallery;

class ImageGalleryUploadRule implements Rule
{
    protected int|null $ignoreID;

    public function __construct(int $ignoreID = null)
    {
        $this->ignoreID = $ignoreID;
    }

    public function passes($attribute, $value): bool
    {
        return !ImageGallery::query()
            ->where('id', $value)
            ->where('occupied', true)
            ->whereNot('section', 'default')
            ->whereNot('id', $this->ignoreID)
            ->exists();
    }

    public function message(): string
    {
        return __('validation.exists');
    }
}
