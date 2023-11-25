<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Gallery\app\Http\Requests\Api\V1\AdminPanel\VideoGallery\VideoGalleryStoreRequest;
use Modules\Gallery\app\Models\VideoGallery;
use Modules\Gallery\app\Resources\V1\AdminPanel\VideoGallery\VideoGalleryCollection;
use Modules\Gallery\app\Resources\V1\AdminPanel\VideoGallery\VideoGalleryResource;
use Modules\Gallery\app\Services\VideoGalleryService;
use Modules\RolePermission\app\Models\Permission;

class VideoGalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.video_gallery.view',]);

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

        return response()->json((new VideoGalleryCollection($videos))->response()->getData(true));
    }

    public function store(VideoGalleryStoreRequest $request, VideoGalleryService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.video_gallery.create',]);

        $alias = $service->getAlias();

//        try {
            $video = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.upload.success', ['attribute' => $alias]),
                'data' => new VideoGalleryResource($video),
            ]);
//        } catch (Exception $exception) {
//            Log::channel('report')->error('VideoGallery store: ' . $exception->getMessage());
//            return response()->json([
//                'message' => __('messages.upload.failure', ['attribute' => $alias]),
//            ], 500);
//        }
    }

    public function show(VideoGallery $video): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.video_gallery.view',]);

        return response()->json([
            'data' => new VideoGalleryResource($video),
        ]);
    }

    public function destroy(VideoGallery $video, VideoGalleryService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.video_gallery.delete',]);

        $alias = $service->getAlias();

        try {
            $service->delete($video);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $alias]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('VideoGallery Destroy: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $alias]),
            ], 500);
        }
    }
}
