<?php

namespace Modules\VersionControl\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
    protected string $permissionPrefix = 'admin_panel.version';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, '$this->permissionPrefix.view']);

        $versions = Version::query()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('title', $v);
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

        return response()->list(VersionCollection::make($versions)->response()->getData(true));
    }

    public function store(VersionStoreRequest $request, VersionService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.create"]);

        try {
            $version = $service->create($request->getSafeData());

            return response()->success(
                message: __('messages.store.success', ['attribute' => $service->getAlias(),]),
                data: VersionResource::make($version)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.store.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function show(Version $version): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        return response()->success(data: VersionResource::make($version));
    }

    public function update(VersionUpdateRequest $request, Version $version, VersionService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $service->update($version, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: VersionResource::make($version)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function destroy(Version $version, VersionService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.delete"]);

        try {
            $service->delete($version);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
