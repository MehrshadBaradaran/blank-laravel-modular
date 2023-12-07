<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\App;

use Auth;
use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Gallery\app\Http\Requests\Api\V1\App\ImageGallery\ImageGalleryStoreRequest;
use Modules\Gallery\app\Models\ImageGallery;
use Modules\Gallery\app\Resources\V1\App\ImageGallery\ImageGalleryCollection;
use Modules\Gallery\app\Resources\V1\App\ImageGallery\ImageGalleryResource;
use Modules\Gallery\app\Services\ImageGalleryService;

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

        return response()->list(ImageGalleryCollection::make($images)->response()->getData(true));
    }

    public function store(ImageGalleryStoreRequest $request, ImageGalleryService $service): JsonResponse
    {
        try {
            $image = $service->create($request->getSafeData());

            return response()->success(
                message: __('messages.upload.success'),
                data: ImageGalleryResource::make($image)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.upload.failure'));
        }
    }

    public function show(ImageGallery $image): JsonResponse
    {
        $this->authorize('view', $image);

        return response()->success(data: ImageGalleryResource::make($image));
    }

    public function destroy(ImageGallery $image, ImageGalleryService $service): JsonResponse
    {
        $this->authorize('delete', $image);

        try {
            $service->delete($image);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
