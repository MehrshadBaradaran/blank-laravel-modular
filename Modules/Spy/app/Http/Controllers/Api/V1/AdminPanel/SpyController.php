<?php

namespace Modules\Spy\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\RolePermission\app\Models\Permission;
use Modules\Spy\app\Models\Spy;
use Modules\Spy\app\Resources\V1\AdminPanel\Spy\SpyCollection;
use Modules\Spy\app\Resources\V1\AdminPanel\Spy\SpyResource;

class SpyController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.spy.view']);

        $spyLogs = Spy::query()
            ->when($request->search, function ($q, $v) {
                $q->where(function ($q)use ($v) {
                    $q->where('title', 'LIKE', "%$v%")
                        ->orWhere('ip_address', 'LIKE', "%$v%");
                });
            })
            ->when($request->user, function ($q, $v) {
                $q->where('user_id', $v);
            })
            ->when($request->permission, function ($q, $v) {
                $q->where('permission_id', $v);
            })
            ->when($request->action, function ($q, $v) {
                $q->where('action', $v);
            })
            ->orderBy('created_at', 'desc');

        $spyLogs = $request->get('paginate', 'true') == 'true'
            ? $spyLogs->paginate($request->get('page_size'))
            : $spyLogs->get();

        return response()->json((new SpyCollection($spyLogs))->response()->getData(true));
    }

    public function show(Spy $spyLog): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.spy.view']);

        return response()->json([
            'data' => new SpyResource($spyLog),
        ]);
    }
}
