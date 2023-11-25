<?php

namespace Modules\Notification\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Notification\app\Models\Notification;
use Modules\User\app\Models\User;

class NotificationDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $notifications = Notification::factory()->count(50)->create();
        $userIdsArray = User::registered()->inRandomOrder()->take(rand(5,50))->pluck('id')->toArray();

        foreach ($notifications as $notification) {
            $notification->users()->attach($userIdsArray);
        }
    }
}
