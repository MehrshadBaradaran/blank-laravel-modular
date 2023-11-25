<?php

namespace Modules\Banner\database\factories;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Banner\app\Models\Banner;

class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        $status = StatusEnum::getValues();
        return [
            'title' => fake()->word(),

            'url' => fake()->url(),

            'status' => $status[array_rand($status)],
        ];
    }
}

