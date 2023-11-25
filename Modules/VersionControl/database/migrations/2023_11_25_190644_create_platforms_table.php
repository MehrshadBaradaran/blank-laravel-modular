<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\VersionControl\app\Enums\PlatformOSEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cover_id')->nullable();

            $table->string('title');
            $table->string('os')
                ->default(PlatformOSEnum::getDefaultCaseValue())
                ->comment(PlatformOSEnum::getDatabaseColumnComment());

            $table->text('url');
            $table->text('cover_paths')->nullable();

            $table->tinyInteger('status')
                ->default(StatusEnum::getDefaultCaseValue())
                ->comment(StatusEnum::getDatabaseColumnComment());

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
        Schema::dropIfExists('platforms');
    }
};
