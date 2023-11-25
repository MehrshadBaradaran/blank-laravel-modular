<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\User\app\Models\User;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('notification_user', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnDelete();
            $table->foreignIdFor(\Modules\Notification\app\Models\Notification::class)
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnDelete();

            $table->tinyInteger('read')->default(0)->comment('0=>false, 1=>true');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_user');
    }
};
