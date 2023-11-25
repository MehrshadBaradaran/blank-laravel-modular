<?php

namespace Modules\ContactUs\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;
use Modules\ContactUs\app\Http\Requests\Api\V1\AdminPanel\ContactUs\ContactUsUpdateRequest;
use Modules\ContactUs\app\Models\ContactUs;
use Modules\ContactUs\app\Resources\V1\AdminPanel\ContactUs\ContactUsResource;
use Modules\ContactUs\app\Services\ContactUsService;
use Modules\RolePermission\app\Models\Permission;

class ContactUsController extends Controller
{
    public function __construct()
    {
        $this->instance = ContactUs::firstOrFail();
    }

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.contact_us.view']);

        $cacheKey = config('contactus.cache_key');

        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : $this->instance;

        return response()->json([
            'data' => new ContactUsResource($data),
        ]);
    }

    public function update(ContactUsUpdateRequest $request, ContactUsService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.contact_us.update']);

        try {
            $service->update($this->instance, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new ContactUsResource($this->instance),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Setting update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $this->alias,]),
            ], 500);
        }
    }
}
