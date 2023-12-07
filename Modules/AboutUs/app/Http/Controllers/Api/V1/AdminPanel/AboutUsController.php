<?php

namespace Modules\AboutUs\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Exception;
use Modules\AboutUs\app\Http\Requests\Api\V1\AdminPanel\AboutUs\AboutUsUpdateRequest;
use Modules\AboutUs\app\Models\AboutUs;
use Modules\AboutUs\app\Resources\V1\AdminPanel\AboutUs\AboutUsResource;
use Modules\AboutUs\app\Services\AboutUsService;
use Modules\RolePermission\app\Models\Permission;

class AboutUsController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.about_us';

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $cacheKey = config('aboutus.cache_key');
        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : AboutUs::firstOrFail();

        return response()->success(data: AboutUsResource::make($data));
    }

    public function update(AboutUsUpdateRequest $request, AboutUsService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $about = AboutUs::firstOrFail();
            $service->update($about, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: AboutUsResource::make($about)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
