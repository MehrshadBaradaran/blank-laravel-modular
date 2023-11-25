<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Gallery\app\Models\ImageGallery;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('avatar_id')->nullable();

            $table->string('phone');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            $table->tinyInteger('is_registered')->default(0)->comment('0=>false, 1=>true');
            $table->tinyInteger('is_admin')->default(0)->comment('0=>false, 1=>true');
            $table->tinyInteger('status')
                ->default(StatusEnum::getDefaultCaseValue())
                ->comment(StatusEnum::getDatabaseColumnComment());

            $table->string('password');
            $table->rememberToken();

            $table->json('avatar_paths')->nullable();

            $table->timestamp('last_login')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};
