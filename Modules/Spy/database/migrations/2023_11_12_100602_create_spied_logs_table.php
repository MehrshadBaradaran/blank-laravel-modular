<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\RolePermission\app\Models\Permission;
use Modules\Spy\app\Enums\SpyActionEnum;
use Modules\User\app\Models\User;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spied_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained();
            $table->foreignIdFor(Permission::class)->nullable()->constrained();
            $table->nullableMorphs('target');

            $table->ipAddress()->nullable();

            $table->string('title')->nullable();
            $table->string('request_method')->nullable();
            $table->string('client_app_version')->nullable();
            $table->string('action')
                ->default(SpyActionEnum::getDefaultCaseValue())
                ->comment(SpyActionEnum::getDatabaseColumnComment());

            $table->text('description')->nullable();
            $table->text('request_url')->nullable();

            $table->json('request_device_data')->nullable();
            $table->json('request_data')->nullable();
            $table->json('user_data')->nullable();
            $table->json('target_data')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spied_logs');
    }
};
