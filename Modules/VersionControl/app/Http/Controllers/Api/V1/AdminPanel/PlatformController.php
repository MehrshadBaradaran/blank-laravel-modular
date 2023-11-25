<?php

namespace Modules\VersionControl\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\VersionControl\app\Http\Requests\Api\V1\AdminPanel\Platform\PlatformStoreRequest;
use Modules\VersionControl\app\Http\Requests\Api\V1\AdminPanel\Platform\PlatformUpdateRequest;
use Modules\VersionControl\app\Models\Platform;
use Modules\VersionControl\app\Resources\V1\AdminPanel\Platform\PlatformCollection;
use Modules\VersionControl\app\Resources\V1\AdminPanel\Platform\PlatformResource;
use Modules\VersionControl\app\Services\PlatformService;
use Modules\RolePermission\app\Models\Permission;

class PlatformController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.platform.view']);

        $platforms = Platform::query()
            ->when($request->search, function ($q, $v) {
                $q->where('title', 'LIKE', "%$v%");
            })
            ->when($request->os, function ($q, $v) {
                $q->where('os', $v);
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v == 'true');
            })
            ->orderBy('created_at', 'desc');


        $platforms = $request->get('paginate', 'true') == 'true'
            ? $platforms->paginate($request->get('page_size'))
            : $platforms->get();

        return response()->json((new PlatformCollection($platforms))->response()->getData(true));
    }

    public function store(PlatformStoreRequest $request, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.platform.create']);

        try {
            $platform = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.store.success', ['attribute' => $service->getAlias(),]),
                'data' => new PlatformResource($platform),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Platform store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.store.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function show(Platform $platform): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.platform.view']);

        return response()->json([
            'data' => new PlatformResource($platform),
        ]);
    }

    public function update(PlatformUpdateRequest $request, Platform $platform, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.platform.update']);

        try {
            $service->update($platform, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new PlatformResource($platform),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Platform update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function destroy(Platform $platform, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.platform.delete']);

        try {
            $service->delete($platform);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Platform destroy: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function changeStatus(Platform $platform, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.platform.change-status']);

        try {
            $platform = $service->changeStatus($platform, !$platform->status->value);

            return response()->json([
                'message' => __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $platform->status->getAlias(),
                ]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Platform status: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.status-change.failure'),
            ], 500);
        }
    }
}
