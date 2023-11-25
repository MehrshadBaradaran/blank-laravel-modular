<?php

namespace Modules\Gallery\app\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Gallery\app\Models\VideoGallery;
use Exception;
use Log;

class DeleteUnoccupiedVideosJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(): void
    {
        try {
            VideoGallery::query()
                ->unoccupied()
                ->where('created_at', '>', now()->addDay())
                ->delete();

        } catch (Exception $e) {
            Log::channel('bug_report')->error("delete unoccupied videos: {$e->getMessage()}");
        }
    }
}
