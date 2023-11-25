<?php

namespace Modules\Spy\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class SpyDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
