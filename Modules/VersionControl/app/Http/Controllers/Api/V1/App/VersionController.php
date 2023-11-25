<?php

namespace Modules\VersionControl\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\VersionControl\app\Models\Platform;
use Modules\VersionControl\app\Models\Version;
use Modules\VersionControl\app\Resources\V1\App\Version\VersionResource;
use Modules\VersionControl\app\Resources\V1\App\Platform\PlatformCollection;

class VersionController extends Controller
{
    public function __invoke($os, $version): JsonResponse
    {
        $latestVersion = Version::query()
            ->where('version_number', '>', $version)
            ->first();

        $platforms = Platform::active()
            ->where('os', $os)
            ->get();

        return response()->json([
            'new_version' => isset($latestVersion),
            'version' => isset($latestVersion) ? new VersionResource($latestVersion) : (object)[],
            'platforms' => new PlatformCollection($platforms),
        ]);
    }
}
