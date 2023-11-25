<?php

namespace Modules\VersionControl\database\factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\VersionControl\app\Models\Version;

class VersionFactory extends Factory
{
    protected $model = Version::class;

    public function definition(): array
    {
        return [
            'title' => fake()->word,
            'description' => fake()->realText,
            'force_update' => rand(0, 1),
        ];
    }
}

