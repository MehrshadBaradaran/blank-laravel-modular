<?php

namespace Modules\Spy\app\Resources\V1\AdminPanel\Spy;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use JsonSerializable;

class SpyCollection extends ResourceCollection
{
    public function toArray($request): array|Collection|JsonSerializable|Arrayable
    {
        return [
            'data' => $this->collection,
        ];
    }
}
