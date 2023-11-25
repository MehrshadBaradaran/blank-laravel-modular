<?php

namespace Modules\FAQ\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\FAQ\app\Models\FAQ;

class FAQDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        FAQ::withoutEvents(function () {
            FAQ::factory()->count(50)->create();
        });
    }
}
