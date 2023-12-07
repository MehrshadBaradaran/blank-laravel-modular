<?php

namespace Modules\Gallery\app\Http\Controllers\Api\V1\AdminPanel;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Gallery\app\Http\Requests\Api\V1\AdminPanel\ImageGallery\ImageGalleryStoreRequest;
use Modules\Gallery\app\Models\ImageGallery;
use Modules\Gallery\app\Resources\V1\AdminPanel\ImageGallery\ImageGalleryCollection;
use Modules\Gallery\app\Resources\V1\AdminPanel\ImageGallery\ImageGalleryResource;
use Modules\Gallery\app\Services\ImageGalleryService;
use Modules\RolePermission\app\Models\Permission;

class ImageGalleryController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.image_gallery';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.view",]);

        $images = ImageGallery::query()
            ->when($request->section, function ($q, $v) {
                $q->where('section', $v);
            })
            ->when($request->occupied, function ($q, $v) {
                $q->where('occupied', $v == 'true');
            })
            ->orderBy('created_at', 'desc');

        $images = $request->get('paginate', 'true') == 'true'
            ? $images->paginate($request->get('page_size'))
            : $images->get();

        return response()->list(ImageGalleryCollection::make($images)->response()->getData(true));
    }

    public function store(ImageGalleryStoreRequest $request, ImageGalleryService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.create",]);

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
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.view",]);

        return response()->success(data: ImageGalleryResource::make($image));
    }

    public function destroy(ImageGallery $image, ImageGalleryService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.delete",]);

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
