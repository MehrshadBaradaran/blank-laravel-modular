<?php

namespace Modules\ContactUs\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\ContactUs\app\Models\ContactUs;
use Modules\ContactUs\app\Resources\V1\App\ContactUs\ContactUsResource;

class ContactUsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $cacheKey = config('contactus.cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) :  ContactUs::firstOrFail();

        return response()->json([
            'data' => new ContactUsResource($data),
        ]);
    }
}
