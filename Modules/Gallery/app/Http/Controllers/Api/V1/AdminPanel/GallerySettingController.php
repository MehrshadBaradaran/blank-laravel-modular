<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Exception;
use Modules\Gallery\app\Http\Requests\Api\V1\AdminPanel\GallerySetting\GallerySettingUpdateRequest;
use Modules\Gallery\app\Models\GallerySetting;
use Modules\Gallery\app\Resources\V1\AdminPanel\GallerySetting\GallerySettingResource;
use Modules\Gallery\app\Services\GallerySettingService;
use Modules\RolePermission\app\Models\Permission;

class GallerySettingController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.gallery_setting';

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $cacheKey = config('gallery.setting_cache_key');
        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : GallerySetting::firstOrFail();

        return response()->success(data: GallerySettingResource::make($data));
    }

    public function update(GallerySettingUpdateRequest $request, GallerySettingService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $gallerySetting = GallerySetting::firstOrFail();
            $service->update($gallerySetting, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: GallerySettingResource::make($gallerySetting)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
