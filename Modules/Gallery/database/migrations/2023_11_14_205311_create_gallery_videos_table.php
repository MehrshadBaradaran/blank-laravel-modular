<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Gallery\app\Enums\ImageGallerySectionEnum;
use Modules\User\app\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->nullable()->constrained()->cascadeOnDelete()->cascadeOnDelete();

            $table->string('section')->default(ImageGallerySectionEnum::getDefaultCaseValue());

            $table->bigInteger('duration')->default(0);
            $table->bigInteger('size')->default(0);

            $table->text('folder')->comment('Path of folder');
            $table->json('files')->comment('Path of files');

            $table->tinyInteger('occupied')->default(0)->comment('0=>false, 1=>true');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_videos');
    }
};
