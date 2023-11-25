<?php

namespace Modules\Setting\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\Setting\app\Http\Requests\Api\V1\AdminPanel\Setting\SettingUpdateRequest;
use Modules\Setting\app\Models\Setting;
use Modules\Setting\app\Resources\V1\AdminPanel\Setting\SettingResource;
use Modules\Setting\app\Services\SettingService;
use Modules\RolePermission\app\Models\Permission;

class SettingController extends Controller
{
    protected string $permissionGroup = 'setting';

    public function __construct()
    {
        $this->instance = Setting::firstOrFail();
    }

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.view"]);

        $cacheKey = config('setting.cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : $this->instance;

        return response()->json([
            'data' => new SettingResource($data),
        ]);
    }

    public function update(SettingUpdateRequest $request, SettingService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.update"]);

        try {
            $service->update($this->instance, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new SettingResource($this->instance),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Setting update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $this->alias,]),
            ], 500);
        }
    }
}
