<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Gallery\app\Models\GallerySetting;
use Modules\Gallery\app\Services\GallerySettingService;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gallery_settings', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('max_image_upload_size')->default(0)->comment('in KB');
            $table->bigInteger('max_video_upload_size')->default(0)->comment('in KB');

            $table->json('image_generate_patterns')->nullable();

            $table->timestamps();
        });

        (new GallerySettingService())->initialize();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_settings');
    }
};
