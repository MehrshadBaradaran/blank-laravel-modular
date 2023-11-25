<?php

namespace Modules\RolePermission\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Modules\RolePermission\app\Http\Requests\Api\V1\AdminPanel\Role\RoleStoreRequest;
use Modules\RolePermission\app\Http\Requests\Api\V1\AdminPanel\Role\RoleUpdateRequest;
use Modules\RolePermission\app\Models\Permission;
use Modules\RolePermission\app\Models\Role;
use Modules\RolePermission\app\Resources\V1\AdminPanel\Role\RoleCollection;
use Modules\RolePermission\app\Resources\V1\AdminPanel\Role\RoleDetailResource;
use Modules\RolePermission\app\Services\RoleService;

class RoleController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.role.view',]);

        $roles = Role::query()
            ->visible()
            ->when($request->search, function ($q, $v) {
                $q->where('name', 'LIKE', "%$v%");
            })
            ->orderBy('created_at', 'desc');

        $roles = $request->get('paginate', 'true') == 'true'
            ? $roles->paginate($request->get('page_size'))
            : $roles->get();

        return response()->json((new RoleCollection($roles))->response()->getData(true));
    }

    public function store(RoleStoreRequest $request, RoleService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.role.create',]);

        try {
            $role = $service->create($request->safeData(), $request->permissions);

            return response()->json([
                'message' => __('messages.store.success', ['attribute' => $service->getAlias()]),
                'data' => new RoleDetailResource($role),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Role store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.store.failure', ['attribute' => $service->getAlias()]),
            ], 500);
        }
    }

    public function show(Role $role): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.role.view',]);
        $this->authorize('check-visibility', $role);

        return response()->json([
            'data' => new RoleDetailResource($role)
        ]);
    }

    public function update(RoleUpdateRequest $request, Role $role, RoleService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.role.update',]);
        $this->authorize('check-visibility', $role);

        try {
            $role = $service->update($role, $request->safeData(), $request->permissions);

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias()]),
                'data' => new RoleDetailResource($role),
            ]);
        } catch (Exception $exception) {
            Log::channel('report')->error('Role update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias()]),
            ], 500);
        }
    }

    public function destroy(Role $role, RoleService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, 'admin_panel.role.delete',]);
        $this->authorize('check-visibility', $role);

        try {
            $service->delete($role);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $service->getAlias()]),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('Role delete: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $service->getAlias()]),
            ], 500);
        }
    }
}
