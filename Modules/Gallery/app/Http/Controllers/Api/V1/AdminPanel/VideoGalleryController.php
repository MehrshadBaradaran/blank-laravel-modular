<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Gallery\app\Http\Requests\Api\V1\AdminPanel\VideoGallery\VideoGalleryStoreRequest;
use Modules\Gallery\app\Models\VideoGallery;
use Modules\Gallery\app\Resources\V1\AdminPanel\VideoGallery\VideoGalleryCollection;
use Modules\Gallery\app\Resources\V1\AdminPanel\VideoGallery\VideoGalleryResource;
use Modules\Gallery\app\Services\VideoGalleryService;
use Modules\RolePermission\app\Models\Permission;

class VideoGalleryController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.video_gallery';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.view",]);

        $videos = VideoGallery::query()
            ->when($request->section, function ($q, $v) {
                $q->where('section', $v);
            })
            ->when($request->occupied, function ($q, $v) {
                $q->where('occupied', $v == 'true');
            })
            ->orderBy('created_at', 'desc');

        $videos = $request->get('paginate', 'true') == 'true'
            ? $videos->paginate($request->get('page_size'))
            : $videos->get();

        return response()->list(VideoGalleryCollection::make($videos)->response()->getData(true));
    }

    public function store(VideoGalleryStoreRequest $request, VideoGalleryService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.create",]);

        try {
            $video = $service->create($request->getSafeData());

            return response()->success(
                message: __('messages.upload.success'),
                data: VideoGalleryResource::make($video)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.upload.failure'));
        }
    }

    public function show(VideoGallery $video): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.view",]);

        return response()->success(data: VideoGalleryResource::make($video));
    }

    public function destroy(VideoGallery $video, VideoGalleryService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.delete",]);

        try {
            $service->delete($video);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
