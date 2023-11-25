<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\App;

use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\Gallery\app\Http\Requests\Api\V1\App\ImageGallery\ImageGalleryStoreRequest;
use Modules\Gallery\app\Models\ImageGallery;
use Modules\Gallery\app\Resources\V1\App\ImageGallery\ImageGalleryCollection;
use Modules\Gallery\app\Resources\V1\App\ImageGallery\ImageGalleryResource;
use Modules\Gallery\app\Services\ImageGalleryService;
use Modules\RolePermission\app\Models\Permission;

class ImageGalleryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $images = Auth::user()
            ->uploadedImages()
            ->orderBy('created_at', 'desc');

        $images = $request->get('paginate', 'true') == 'true'
            ? $images->paginate($request->get('page_size'))
            : $images->get();

        return response()->json((new ImageGalleryCollection($images))->response()->getData(true));
    }

    public function store(ImageGalleryStoreRequest $request, ImageGalleryService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.image_gallery.create',]);

        $alias = $service->getAlias();

        try {
            $gallery = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.upload.success', ['attribute' => $alias]),
                'data' => new ImageGalleryResource($gallery),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('ImageGallery store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.upload.failure'),
            ], 500);
        }
    }

    public function show(ImageGallery $image): JsonResponse
    {
        $this->authorize('view', $image);

        return response()->json([
            'data' => new ImageGalleryResource($image),
        ]);
    }

    public function destroy(ImageGallery $image, ImageGalleryService $service): JsonResponse
    {
        $this->authorize('delete', $image);

        $alias = $service->getAlias();

        try {
            $service->delete($image);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $alias]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('ImageGallery Destroy: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $alias]),
            ], 500);
        }
    }
}
