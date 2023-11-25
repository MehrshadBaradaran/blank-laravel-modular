<?php

namespace Modules\Spy\app\Services;

use Modules\Spy\app\Models\Spy;

class SpyService
{
    public function getAlias(): string
    {
        return __('spy::alias.name.spy');
    }

    public function store(array $data): Spy
    {
        return Spy::create($data);
    }
}
