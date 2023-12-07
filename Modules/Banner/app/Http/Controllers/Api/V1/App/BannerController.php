<?php

namespace Modules\Banner\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Banner\app\Models\Banner;
use Modules\Banner\app\Resources\V1\app\Banner\BannerCollection;
use Modules\Banner\app\Resources\V1\app\Banner\BannerResource;

class BannerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $banners = Banner::query()
            ->active()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('title', $v);
            })
            ->orderBy('created_at', 'desc');

        $banners = $request->get('paginate', 'true') == 'true'
            ? $banners->paginate($request->get('page_size'))
            : $banners->get();

        return response()->list(BannerCollection::make($banners)->response()->getData(true));
    }

    public function show(Banner $banner): JsonResponse
    {
        return response()->success(data: BannerResource::make($banner));
    }
}
