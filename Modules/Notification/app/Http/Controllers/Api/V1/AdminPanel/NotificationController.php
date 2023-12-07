<?php

namespace Modules\Notification\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Modules\Notification\app\Http\Requests\Api\V1\AdminPanel\Notification\NotificationStoreRequest;
use Modules\Notification\app\Http\Requests\Api\V1\AdminPanel\Notification\NotificationUpdateRequest;
use Modules\Notification\app\Models\Notification;
use Modules\Notification\app\Resources\V1\AdminPanel\Notification\NotificationCollection;
use Modules\Notification\app\Resources\V1\AdminPanel\Notification\NotificationDetailResource;
use Modules\Notification\app\Services\NotificationService;
use Modules\RolePermission\app\Models\Permission;

class NotificationController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.notification';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $notifications = Notification::query()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('title', $v);
            })
            ->when($request->users, function ($q, $v) {
                $q->whereHas('users', function ($q) use ($v) {
                    $q->whereIn('users.id', explode(',', $v));
                });
            })
            ->when($request->type, function ($q, $v) {
                $q->where('type', $v);
            })
            ->when($request->infrom_type, function ($q, $v) {
                $q->where('inform_type', $v);
            })
            ->when($request->general, function ($q, $v) {
                $q->where('general', $v == 'true');
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v == 'true');
            })
            ->orderBy('created_at', 'desc');


        $notifications = $request->get('paginate', 'true') == 'true'
            ? $notifications->paginate($request->get('page_size'))
            : $notifications->get();

        return response()->list(NotificationCollection::make($notifications)->response()->getData(true));
    }

    public function store(NotificationStoreRequest $request, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.create"]);

        try {
            $notification = $service->create($request->safeData(), $request->users);

            return response()->success(
                message: __('messages.store.success', ['attribute' => $service->getAlias(),]),
                data: NotificationDetailResource::make($notification)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.store.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function show(Notification $notification): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        return response()->success(data: NotificationDetailResource::make($notification));
    }

    public function update(NotificationUpdateRequest $request, Notification $notification, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);

        try {
            $service->update($notification, $request->safeData(), $request->users);

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: NotificationDetailResource::make($notification)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function destroy(Notification $notification, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.delete"]);

        try {
            $service->delete($notification);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function changeStatus(Notification $notification, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.change-status"]);

        try {
            $notification = $service->changeStatus($notification, !$notification->status->value);

            return response()->success(
                message: __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $notification->status->getAlias(),
                ]),
                data: NotificationDetailResource::make($notification)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.status-change.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
