<?php

namespace Modules\User\app\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Log;
use Modules\User\app\Models\User;

class DeleteUnregisteredUsersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        try {
            User::query()
                ->unregistered()
                ->where('created_at', '>', now()->addDays(7))
                ->forceDelete();
        } catch (Exception $e) {
            Log::channel('bug_report')->error("delete unregistered users: {$e->getMessage()}");
        }
    }
}
