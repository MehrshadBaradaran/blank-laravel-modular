<?php

namespace Modules\User\app\Console;

use App\Utilities\StrGen;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Exception;
use Modules\RolePermission\app\Models\Role;
use Modules\User\app\Models\User;

class AssignSuperAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a super admin user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(): void
    {
        if (!Role::where('name', 'super_admin')->exists()) {
            $this->newLine();
            $this->error('No role named super_admin found!');
            $this->newLine();
            $this->info('Run `php artisan permission:init` command');
            $this->newLine();
            die();
        }

        try {
            $superAdminUser = DB::transaction(function () {
                $first_name = $this->ask('first name: ', StrGen::lowercase(8)->get());
                $last_name = $this->ask('last name: ', StrGen::lowercase(8)->get());
                $phone = $this->ask('phone: ', '09' . StrGen::number(9)->get());
                $password = $this->secret('password: ');

                $superAdminUser = User::create([
                    'first_name' => $first_name,
                    'last_name' => $last_name,

                    'phone' => $phone,
                    'password' => $password,

                    'phone_verified_at' => now(),

                    'is_admin' => true,
                    'is_registered' => true,
                ]);

                $superAdminUser->assignRole('super_admin');

                return $superAdminUser;
            });

            $this->info("super admin created successfully");
            if ($this->confirm('Do you need an auth token?')) {

                $token = $superAdminUser->createToken('auth_token')->accessToken;

                $this->info("token: $token");
            }

        } catch (Exception $exception) {
            $this->error("Something went wrong!");
            $this->error($exception);
        }
    }
}
