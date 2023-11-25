<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\Gallery\app\Http\Requests\Api\V1\AdminPanel\GallerySetting\GallerySettingUpdateRequest;
use Modules\Gallery\app\Models\GallerySetting;
use Modules\Gallery\app\Resources\V1\AdminPanel\GallerySetting\GallerySettingResource;
use Modules\Gallery\app\Services\GallerySettingService;
use Modules\RolePermission\app\Models\Permission;

class GallerySettingController extends Controller
{
    protected string $permissionGroup = 'gallery_setting';

    public function __construct()
    {
        $this->instance = GallerySetting::firstOrFail();
    }

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.view"]);

        $cacheKey = config('gallery.setting_cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : $this->instance;

        return response()->json([
            'data' => new GallerySettingResource($data),
        ]);
    }

    public function update(GallerySettingUpdateRequest $request, GallerySettingService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.update"]);

        try {
            $service->update($this->instance, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new GallerySettingResource($this->instance),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Setting update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $this->alias,]),
            ], 500);
        }
    }
}
