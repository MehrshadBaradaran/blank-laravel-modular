<?php

namespace Modules\Banner\database\seeders;

use Illuminate\Database\Seeder;
use Modules\Banner\app\Models\Banner;

class BannerDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Banner::withoutEvents(function () {
            Banner::factory()->count(50)->create();
        });
    }
}
