<?php

namespace Modules\FAQ\app\Resources\V1\AdminPanel\FAQ;

use Illuminate\Http\Resources\Json\JsonResource;

class FAQResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'sort_index' => $this->sort_index,

            'question' => $this->question,
            'answer' => $this->answer,

            'status' => $this->status->getBoolValue(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
