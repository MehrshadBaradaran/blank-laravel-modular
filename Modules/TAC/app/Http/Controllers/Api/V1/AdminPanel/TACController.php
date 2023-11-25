<?php

namespace Modules\TAC\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\TAC\app\Http\Requests\Api\V1\AdminPanel\TAC\TACUpdateRequest;
use Modules\TAC\app\Models\TAC;
use Modules\TAC\app\Resources\V1\AdminPanel\TAC\TACResource;
use Modules\TAC\app\Services\TACService;
use Modules\RolePermission\app\Models\Permission;

class TACController extends Controller
{
    protected string $permissionGroup = 'tac';
    protected TAC $instance;

    public function __construct()
    {
        $this->instance = TAC::firstOrFail();
    }

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.view"]);

        $cacheKey = config('tac.cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : $this->instance;

        return response()->json([
            'data' => new TACResource($data),
        ]);
    }

    public function update(TACUpdateRequest $request, TACService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "admin_panel.$this->permissionGroup.update"]);

        try {
            $service->update($this->instance, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new TACResource($this->instance),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Setting update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $this->alias,]),
            ], 500);
        }
    }
}
