<?php

namespace Modules\User\database\factories;

use App\Enums\StatusEnum;
use App\Utilities\StrGen;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Modules\User\app\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        $status = StatusEnum::getValues();
        $date = fake()->dateTime;

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),

            'phone' => '98' . StrGen::number(9)->get(),
            'password' => Hash::make('password'),

            'is_admin' => rand(0, 1),
            'is_registered' => rand(0, 1),
            'status' => $status[array_rand($status)],

            'phone_verified_at' => $date,
            'last_login' => $date,
        ];
    }
}

