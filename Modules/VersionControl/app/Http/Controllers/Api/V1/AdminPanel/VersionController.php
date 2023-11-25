<?php

namespace Modules\VersionControl\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\VersionControl\app\Http\Requests\Api\V1\AdminPanel\Version\VersionStoreRequest;
use Modules\VersionControl\app\Http\Requests\Api\V1\AdminPanel\Version\VersionUpdateRequest;
use Modules\VersionControl\app\Models\Version;
use Modules\VersionControl\app\Resources\V1\AdminPanel\Version\VersionCollection;
use Modules\VersionControl\app\Resources\V1\AdminPanel\Version\VersionResource;
use Modules\VersionControl\app\Services\VersionService;
use Modules\RolePermission\app\Models\Permission;

class VersionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.version.view']);

        $versions = Version::query()
            ->when($request->search, function ($q, $v) {
                $q->where('title', 'LIKE', "%$v%");
            })
            ->when($request->os, function ($q, $v) {
                $q->where('os', $v);
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v == 'true');
            })
            ->with('platform')
            ->orderBy('created_at', 'desc');


        $versions = $request->get('paginate', 'true') == 'true'
            ? $versions->paginate($request->get('page_size'))
            : $versions->get();

        return response()->json((new VersionCollection($versions))->response()->getData(true));
    }

    public function store(VersionStoreRequest $request, VersionService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.version.create']);

        try {
            $version = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.store.success', ['attribute' => $service->getAlias(),]),
                'data' => new VersionResource($version),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Version store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.store.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function show(Version $version): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.version.view']);

        return response()->json([
            'data' => new VersionResource($version),
        ]);
    }

    public function update(VersionUpdateRequest $request, Version $version, VersionService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.version.update']);

        try {
            $service->update($version, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new VersionResource($version),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Version update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function destroy(Version $version, VersionService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.version.delete']);

        try {
            $service->delete($version);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Version destroy: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }
}
