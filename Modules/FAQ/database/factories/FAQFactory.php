<?php

namespace Modules\FAQ\database\factories;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\FAQ\app\Models\FAQ;

class FAQFactory extends Factory
{
    protected $model = FAQ::class;

    public function definition(): array
    {
        $status = StatusEnum::getValues();

        return [
            'question' => fake()->realText . '?',
            'answer' => fake()->realText,

            'status' => $status[array_rand($status)],
        ];
    }
}

