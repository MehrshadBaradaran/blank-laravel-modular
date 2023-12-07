<?php

namespace Modules\VersionControl\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    protected string $permissionPrefix = 'admin_panel.platform';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $platforms = Platform::query()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('title', $v);
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

        return response()->list(PlatformCollection::make($platforms)->response()->getData(true));
    }

    public function store(PlatformStoreRequest $request, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.create"]);

        try {
            $platform = $service->create($request->getSafeData());

            return response()->success(
                message: __('messages.store.success', ['attribute' => $service->getAlias(),]),
                data: PlatformResource::make($platform)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.store.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function show(Platform $platform): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        return response()->success(data: PlatformResource::make($platform));
    }

    public function update(PlatformUpdateRequest $request, Platform $platform, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $service->update($platform, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: PlatformResource::make($platform)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function destroy(Platform $platform, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.delete"]);

        try {
            $service->delete($platform);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function changeStatus(Platform $platform, PlatformService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.change-status"]);

        try {
            $platform = $service->changeStatus($platform, !$platform->status->value);

            return response()->success(
                message: __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $platform->status->getAlias(),
                ]),
                data: PlatformResource::make($platform)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.status-change.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
