<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Modules\Banner\database\seeders\BannerDatabaseSeeder;
use Modules\FAQ\database\seeders\FAQDatabaseSeeder;
use Modules\Notification\database\seeders\NotificationDatabaseSeeder;
use Modules\User\database\seeders\UserDatabaseSeeder;
use Modules\VersionControl\database\seeders\VersionControlDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(NotificationDatabaseSeeder::class);
        $this->call(UserDatabaseSeeder::class);
        $this->call(BannerDatabaseSeeder::class);
        $this->call(FAQDatabaseSeeder::class);
        $this->call(VersionControlDatabaseSeeder::class);
    }
}
