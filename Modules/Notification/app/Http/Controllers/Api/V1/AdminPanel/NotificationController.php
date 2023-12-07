<?php

namespace Modules\Notification\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.notification.view']);

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

        return response()->json((new NotificationCollection($notifications))->response()->getData(true));
    }

    public function store(NotificationStoreRequest $request, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.notification.create']);

        try {
            $notification = $service->create($request->safeData(), $request->users);

            return response()->json([
                'message' => __('messages.store.success', ['attribute' => $service->getAlias(),]),
                'data' => new NotificationDetailResource($notification),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Notification store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.store.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function show(Notification $notification): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.notification.view']);

        return response()->json([
            'data' => new NotificationDetailResource($notification),
        ]);
    }

    public function update(NotificationUpdateRequest $request, Notification $notification, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.notification.update']);

        try {
            $service->update($notification, $request->safeData(), $request->users);

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new NotificationDetailResource($notification),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Notification update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function destroy(Notification $notification, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.notification.delete']);

        try {
            $service->delete($notification);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Notification destroy: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function changeStatus(Notification $notification, NotificationService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.notification.change-status']);

        try {
            $notification = $service->changeStatus($notification, !$notification->status->value);

            return response()->json([
                'message' => __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $notification->status->getAlias(),
                ]),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Notification status: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.status-change.failure'),
            ], 500);
        }
    }
}
