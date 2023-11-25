<?php

namespace Modules\Setting\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SettingDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
