<?php

namespace Modules\Notification\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Notification\app\Models\Notification;
use Modules\Notification\app\Resources\V1\app\Notification\NotificationCollection;
use Modules\Notification\app\Resources\V1\app\Notification\NotificationResource;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Auth::user()
            ->notifications()
            ->active()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('title', $v);
            })
            ->when($request->infrom_type, function ($q, $v) {
                $q->where('inform_type', $v);
            })
            ->orderBy('created_at', 'desc');

        $notifications = $request->get('paginate', 'true') == 'true'
            ? $notifications->paginate($request->get('page_size'))
            : $notifications->get();

        return response()->list(NotificationCollection::make($notifications)->response()->getData(true));
    }

    public function show(Notification $notification): JsonResponse
    {
        $this->authorize('viewAny', [Notification::class, $notification]);

        $notification->markAsRead();

        return response()->success(data: NotificationResource::make($notification));
    }
}
