<?php

namespace Modules\TAC\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class TACDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
