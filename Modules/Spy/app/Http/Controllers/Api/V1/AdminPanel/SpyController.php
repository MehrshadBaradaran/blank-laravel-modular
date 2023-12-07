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
    protected string $permissionPrefix = 'admin_panel.spy';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $spyLogs = Spy::query()
            ->when($request->search, function ($q, $v) {
                $q->whereLike(['title', 'ip_address',], $v);
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
            ->with(['user', 'target', 'permission',])
            ->orderBy('created_at', 'desc');

        $spyLogs = $request->get('paginate', 'true') == 'true'
            ? $spyLogs->paginate($request->get('page_size'))
            : $spyLogs->get();

        return response()->list(SpyCollection::make($spyLogs)->response()->getData(true));
    }

    public function show(Spy $spyLog): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        return response()->success(data: SpyResource::make($spyLog));
    }
}
