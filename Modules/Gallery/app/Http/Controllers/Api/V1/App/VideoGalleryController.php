<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\App;

use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Gallery\app\Http\Requests\Api\V1\App\VideoGallery\VideoGalleryStoreRequest;
use Modules\Gallery\app\Models\VideoGallery;
use Modules\Gallery\app\Resources\V1\App\VideoGallery\VideoGalleryCollection;
use Modules\Gallery\app\Resources\V1\App\VideoGallery\VideoGalleryResource;
use Modules\Gallery\app\Services\VideoGalleryService;

class VideoGalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $videos = Auth::user()
            ->uploadedVideos()
            ->orderBy('created_at', 'desc');

        $videos = $request->get('paginate', 'true') == 'true'
            ? $videos->paginate($request->get('page_size'))
            : $videos->get();

        return response()->json((new VideoGalleryCollection($videos))->response()->getData(true));
    }

    public function store(VideoGalleryStoreRequest $request, VideoGalleryService $service): JsonResponse
    {
        $alias = $service->getAlias();

        try {
            $video = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.upload.success', ['attribute' => $alias]),
                'data' => new VideoGalleryResource($video),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('VideoGallery store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.upload.failure', ['attribute' => $alias]),
            ], 500);
        }
    }

    public function show(VideoGallery $video): JsonResponse
    {
        $this->authorize('view', $video);

        return response()->json([
            'data' => new VideoGalleryResource($video),
        ]);
    }

    public function destroy(VideoGallery $video, VideoGalleryService $service): JsonResponse
    {
        $this->authorize('delete', $video);

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
