<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\RolePermission\app\Console\PermissionInitializer;
use Modules\User\app\Console\AssignSuperAdminUser;

class CommandServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->commands([
            PermissionInitializer::class,
            AssignSuperAdminUser::class,
        ]);
    }
}
