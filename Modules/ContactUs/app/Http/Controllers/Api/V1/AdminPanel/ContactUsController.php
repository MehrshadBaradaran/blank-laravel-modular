<?php

namespace Modules\ContactUs\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Exception;
use Modules\ContactUs\app\Http\Requests\Api\V1\AdminPanel\ContactUs\ContactUsUpdateRequest;
use Modules\ContactUs\app\Models\ContactUs;
use Modules\ContactUs\app\Resources\V1\AdminPanel\ContactUs\ContactUsResource;
use Modules\ContactUs\app\Services\ContactUsService;
use Modules\RolePermission\app\Models\Permission;

class ContactUsController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.about_us';

    public function index(): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $cacheKey = config('contactus.cache_key');
        $data = Cache::has($cacheKey) ? Cache::get($cacheKey) : ContactUs::firstOrFail();

        return response()->success(data: ContactUsResource::make($data));
    }

    public function update(ContactUsUpdateRequest $request, ContactUsService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $contact = ContactUs::firstOrFail();
            $service->update($contact, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: ContactUsResource::make($contact)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
