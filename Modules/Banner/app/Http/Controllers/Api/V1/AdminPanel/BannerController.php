<?php

namespace Modules\Banner\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.banner.view']);

        $banners = Banner::query()
            ->when($request->search, function ($q, $value) {
                $q->where('title', 'LIKE', "%$value%");
            })
            ->when($request->status, function ($q, $value) {
                $q->where('status', $value == 'true');
            })
            ->orderBy('created_at', 'desc');


        $banners = $request->get('paginate', 'true') == 'true'
            ? $banners->paginate($request->get('page_size'))
            : $banners->get();

        return response()->json((new BannerCollection($banners))->response()->getData(true));
    }

    public function store(BannerStoreRequest $request, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.banner.create']);

        try {
            $banner = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.store.success', ['attribute' => $service->getAlias(),]),
                'data' => new BannerResource($banner),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Banner store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.store.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function show(Banner $banner): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.banner.view']);

        return response()->json([
            'data' => new BannerResource($banner),
        ]);
    }

    public function update(BannerUpdateRequest $request, Banner $banner, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.banner.update']);

        try {
            $service->update($banner, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new BannerResource($banner),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Banner update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function destroy(Banner $banner, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.banner.delete']);

        try {
            $service->delete($banner);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Banner destroy: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function changeStatus(Banner $banner, BannerService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.banner.change-status']);

        try {
            $banner = $service->changeStatus($banner, !$banner->status->value);

            return response()->json([
                'message' => __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $banner->status->getAlias(),
                ]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Banner status: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.status-change.failure'),
            ], 500);
        }
    }
}
