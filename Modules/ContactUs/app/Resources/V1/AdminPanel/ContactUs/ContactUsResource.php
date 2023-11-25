<?php

namespace Modules\ContactUs\app\Resources\V1\AdminPanel\ContactUs;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'updated_at' => $this->updated_at,

            'data' => $this->data,
        ];
    }
}
