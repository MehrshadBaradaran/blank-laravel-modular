<?php

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Modules\Notification\app\Enums\NotificationInformTypeEnum;
use Modules\Notification\app\Enums\NotificationTypeEnum;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('type')
                ->default(NotificationTypeEnum::getDefaultCaseValue())
                ->comment(NotificationTypeEnum::getDatabaseColumnComment());
            $table->string('inform_type')
                ->default(NotificationInformTypeEnum::getDefaultCaseValue())
                ->comment(NotificationInformTypeEnum::getDatabaseColumnComment());

            $table->longText('body');

            $table->tinyInteger('general')
                ->default(0)
                ->comment('0=>false, 1=>true');
            $table->tinyInteger('status')
                ->default(StatusEnum::getDefaultCaseValue())
                ->comment(StatusEnum::getDatabaseColumnComment());

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
