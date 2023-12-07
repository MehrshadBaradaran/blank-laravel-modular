<?php

namespace Modules\TAC\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Exception;
use Modules\TAC\app\Http\Requests\Api\V1\AdminPanel\TAC\TACUpdateRequest;
use Modules\TAC\app\Models\TAC;
use Modules\TAC\app\Resources\V1\AdminPanel\TAC\TACResource;
use Modules\TAC\app\Services\TACService;
use Modules\RolePermission\app\Models\Permission;

class TACController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.tac';

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $cacheKey = config('tac.cache_key');
        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : TAC::firstOrFail();

        return response()->success(data: TACResource::make($data));
    }

    public function update(TACUpdateRequest $request, TACService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $tac = TAC::firstOrFail();
            $service->update($tac, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: TACResource::make($tac)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
