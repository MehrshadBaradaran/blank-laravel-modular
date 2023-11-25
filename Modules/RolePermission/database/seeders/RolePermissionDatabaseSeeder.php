<?php

namespace Modules\RolePermission\database\seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RolePermissionDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");
    }
}
