<?php

namespace Modules\FAQ\app\Resources\V1\App\FAQ;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,

            'question' => $this->question,
            'answer' => $this->answer,
        ];
    }
}
