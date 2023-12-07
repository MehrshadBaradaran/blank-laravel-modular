<?php

namespace Modules\RolePermission\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Http\JsonResponse;
use Modules\RolePermission\app\Http\Requests\Api\V1\AdminPanel\Role\RoleStoreRequest;
use Modules\RolePermission\app\Http\Requests\Api\V1\AdminPanel\Role\RoleUpdateRequest;
use Modules\RolePermission\app\Models\Permission;
use Modules\RolePermission\app\Models\Role;
use Modules\RolePermission\app\Resources\V1\AdminPanel\Role\RoleCollection;
use Modules\RolePermission\app\Resources\V1\AdminPanel\Role\RoleDetailResource;
use Modules\RolePermission\app\Services\RoleService;

class RoleController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.role';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.view",]);

        $roles = Role::query()
            ->visible()
            ->when($request->search, function ($q, $v) {
                $q->whereLike('name', $v);
            })
            ->orderBy('created_at', 'desc');

        $roles = $request->get('paginate', 'true') == 'true'
            ? $roles->paginate($request->get('page_size'))
            : $roles->get();

        return response()->list(RoleCollection::make($roles)->response()->getData(true));
    }

    public function store(RoleStoreRequest $request, RoleService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.create",]);

        try {
            $role = $service->create($request->safeData(), $request->permissions);

            return response()->success(
                message: __('messages.store.success', ['attribute' => $service->getAlias(),]),
                data: RoleDetailResource::make($role)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.store.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function show(Role $role): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.view",]);
        $this->authorize('check-visibility', $role);

        return response()->success(data: RoleDetailResource::make($role));
    }

    public function update(RoleUpdateRequest $request, Role $role, RoleService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.update",]);
        $this->authorize('check-visibility', $role);

        try {
            $role = $service->update($role, $request->safeData(), $request->permissions);

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: RoleDetailResource::make($role)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function destroy(Role $role, RoleService $service): JsonResponse
    {
        $this->authorize('check-permission', [Permission::class, "$this->permissionPrefix.delete",]);
        $this->authorize('check-visibility', $role);

        try {
            $service->delete($role);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
