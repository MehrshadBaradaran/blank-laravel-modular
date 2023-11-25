<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\Gallery\app\Models\GallerySetting;
use Modules\Gallery\app\Resources\V1\App\GallerySetting\GallerySettingResource;
use Modules\RolePermission\app\Models\Permission;

class GallerySettingController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.gallery_setting.view']);

        $cacheKey = config('gallery.setting_cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : GallerySetting::firstOrFail();

        return response()->json([
            'data' => new GallerySettingResource($data),
        ]);
    }
}
