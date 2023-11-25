<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\VersionControl\app\Models\Platform;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('versions', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Platform::class)->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('title');

            $table->integer('version_number');

            $table->text('description')->nullable();

            $table->tinyInteger('force_update')->default(0)->comment('0=>false, 1=>true');

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
        Schema::dropIfExists('versions');
    }
};
