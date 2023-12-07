<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\App;

use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
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

        return response()->list(VideoGalleryCollection::make($videos)->response()->getData(true));
    }

    public function store(VideoGalleryStoreRequest $request, VideoGalleryService $service): JsonResponse
    {
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
        $this->authorize('view', $video);

        return response()->success(data: VideoGalleryResource::make($video));
    }

    public function destroy(VideoGallery $video, VideoGalleryService $service): JsonResponse
    {
        $this->authorize('delete', $video);

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
