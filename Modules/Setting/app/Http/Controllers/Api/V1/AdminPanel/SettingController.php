<?php

namespace Modules\Setting\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Exception;
use Modules\Setting\app\Http\Requests\Api\V1\AdminPanel\Setting\SettingUpdateRequest;
use Modules\Setting\app\Models\Setting;
use Modules\Setting\app\Resources\V1\AdminPanel\Setting\SettingResource;
use Modules\Setting\app\Services\SettingService;
use Modules\RolePermission\app\Models\Permission;

class SettingController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.setting';

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $cacheKey = config('setting.cache_key');
        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : Setting::firstOrFail();

        return response()->success(data: SettingResource::make($data));
    }

    public function update(SettingUpdateRequest $request, SettingService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $setting = Setting::firstOrFail();
            $service->update($setting, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: SettingResource::make($setting)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
