<?php

namespace Modules\TAC\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\TAC\app\Models\TAC;
use Modules\TAC\app\Resources\V1\App\TAC\TACResource;

class TACController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $cacheKey = config('tac.cache_key');
        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : TAC::firstOrFail();

        return response()->success(data: TACResource::make($data));
    }
}
