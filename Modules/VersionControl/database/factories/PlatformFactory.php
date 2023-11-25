<?php

namespace Modules\VersionControl\database\factories;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\VersionControl\app\Enums\PlatformOSEnum;
use Modules\VersionControl\app\Models\Platform;

class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition(): array
    {
        $os = PlatformOSEnum::getValues();
        $status = StatusEnum::getValues();

        return [
            'title' => fake()->word(),
            'url' => fake()->url(),

            'os' => $os[array_rand($os)],
            'status' => $status[array_rand($status)],

            'cover_paths' => [
                'master' => fake()->imageUrl(),
            ],
        ];
    }
}

