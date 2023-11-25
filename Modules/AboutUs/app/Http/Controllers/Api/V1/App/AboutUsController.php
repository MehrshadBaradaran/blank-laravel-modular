<?php

namespace Modules\AboutUs\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\AboutUs\app\Models\AboutUs;
use Modules\AboutUs\app\Resources\V1\App\AboutUs\AboutUsResource;

class AboutUsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $cacheKey = config('aboutus.cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) :  AboutUs::firstOrFail();

        return response()->json([
            'data' => new AboutUsResource($data),
        ]);
    }
}
