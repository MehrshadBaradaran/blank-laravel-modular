<?php

namespace Modules\Banner\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Modules\Banner\app\Http\Requests\Api\V1\AdminPanel\Banner\BannerStoreRequest;
use Modules\Banner\app\Http\Requests\Api\V1\AdminPanel\Banner\BannerUpdateRequest;
use Modules\Banner\app\Models\Banner;
use Modules\Banner\app\Resources\V1\AdminPanel\Banner\BannerCollection;
use Modules\Banner\app\Resources\V1\AdminPanel\Banner\BannerResource;
use Modules\Banner\app\Services\BannerService;
use Modules\RolePermission\app\Models\Permission;

class BannerController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.banner';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $banners = Banner::query()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('title', $v);
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v == 'true');
            })
            ->orderBy('created_at', 'desc');


        $banners = $request->get('paginate', 'true') == 'true'
            ? $banners->paginate($request->get('page_size'))
            : $banners->get();

        return response()->list(BannerCollection::make($banners)->response()->getData(true));
    }

    public function store(BannerStoreRequest $request, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.create"]);

        try {
            $banner = $service->create($request->getSafeData());

            return response()->success(
                message: __('messages.store.success', ['attribute' => $service->getAlias(),]),
                data: BannerResource::make($banner)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.store.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function show(Banner $banner): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        return response()->success(data: BannerResource::make($banner));
    }

    public function update(BannerUpdateRequest $request, Banner $banner, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $service->update($banner, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: BannerResource::make($banner)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function destroy(Banner $banner, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.delete"]);

        try {
            $service->delete($banner);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function changeStatus(Banner $banner, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.change-status"]);

        try {
            $banner = $service->changeStatus($banner, !$banner->status->value);

            return response()->success(
                message: __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $banner->status->getAlias(),
                ]),
                data: BannerResource::make($banner)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.status-change.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
