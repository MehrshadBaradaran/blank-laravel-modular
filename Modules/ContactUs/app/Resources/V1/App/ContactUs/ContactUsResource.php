<?php

namespace Modules\ContactUs\app\Resources\V1\App\ContactUs;

use Illuminate\Http\Resources\Json\JsonResource;

class ContactUsResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
