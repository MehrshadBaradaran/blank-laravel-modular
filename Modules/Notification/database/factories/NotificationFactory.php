<?php

namespace Modules\Notification\database\factories;

use App\Enums\StatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Notification\app\Enums\NotificationInformTypeEnum;
use Modules\Notification\app\Enums\NotificationTypeEnum;
use Modules\Notification\app\Models\Notification;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        $types = NotificationTypeEnum::getValues();
        $informTypes = NotificationInformTypeEnum::getValues();
        $status = StatusEnum::getValues();

        return [
            'title' => fake()->words(2, true),
            'body' => fake()->text(),

            'type' => $types[array_rand($types)],
            'inform_type' => $informTypes[array_rand($informTypes)],

            'status' => $status[array_rand($status)],
            'general' => rand(0, 1),
        ];
    }
}

