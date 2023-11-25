<?php

namespace Modules\RolePermission\app\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Exception;
use Modules\RolePermission\app\Action\AdminPanelPermissions;
use Modules\RolePermission\app\Models\Permission;
use Modules\RolePermission\app\Models\PermissionGroup;
use Modules\RolePermission\app\Models\Role;
use Modules\RolePermission\app\Services\PermissionTypeService;

class PermissionInitializer extends Command
{
    protected array $permissions;
    protected $signature = 'permission:initial';
    protected $description = 'Initialize permissions';

    public function handle(): void
    {
        try {
            // Refresh permissions
            Schema::disableForeignKeyConstraints();
            PermissionGroup::query()->truncate();
            Permission::query()->truncate();
            Schema::enableForeignKeyConstraints();

            Permission::insert(AdminPanelPermissions::get());

            Artisan::call("permission:cache-reset");
            $this->info('Permissions initialized successfully');

            $superAdminRole = Role::where('name', 'super_admin')->first();

            if (!$superAdminRole) {
                if ($this->confirm('Do you wish to make super_admin role?', true)) {
                    $role = Role::create([
                        'name' => 'super_admin',
                        'guard_name' => 'web',
                        'types' => [(new PermissionTypeService())->getAdminTypeName(),],
                        'visible' => false,
                    ]);
                    $role->syncPermissions(Permission::get('id')->toArray());
                }

            } else {
                if ($this->confirm('Do you wish to attach new permissions to super_admin role?', true)) {
                    $superAdminRole->syncPermissions(Permission::get('id')->toArray());
                }
            }

        } catch (Exception $exception) {
            $this->error("Something went wrong!");
            $this->error($exception);
        }
    }
}
