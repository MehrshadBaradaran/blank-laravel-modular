<?php

namespace Modules\Setting\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\Setting\app\Models\Setting;
use Modules\Setting\app\Resources\V1\App\Setting\SettingResource;

class SettingController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $cacheKey = config('setting.cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) :  Setting::firstOrFail();

        return response()->json([
            'data' => new SettingResource($data),
        ]);
    }
}
