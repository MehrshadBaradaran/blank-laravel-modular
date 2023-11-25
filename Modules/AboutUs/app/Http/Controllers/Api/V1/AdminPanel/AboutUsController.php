<?php

namespace Modules\AboutUs\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\AboutUs\app\Http\Requests\Api\V1\AdminPanel\AboutUs\AboutUsUpdateRequest;
use Modules\AboutUs\app\Models\AboutUs;
use Modules\AboutUs\app\Resources\V1\AdminPanel\AboutUs\AboutUsResource;
use Modules\AboutUs\app\Services\AboutUsService;
use Modules\RolePermission\app\Models\Permission;

class AboutUsController extends Controller
{
    protected string $permissionGroup = 'about_us';

    public function __construct()
    {
        $this->instance = AboutUs::firstOrFail();
    }

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.view"]);

        $cacheKey = config('aboutus.cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : $this->instance;

        return response()->json([
            'data' => new AboutUsResource($data),
        ]);
    }

    public function update(AboutUsUpdateRequest $request, AboutUsService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.update"]);

        try {
            $service->update($this->instance, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new AboutUsResource($this->instance),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Setting update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $this->alias,]),
            ], 500);
        }
    }
}
