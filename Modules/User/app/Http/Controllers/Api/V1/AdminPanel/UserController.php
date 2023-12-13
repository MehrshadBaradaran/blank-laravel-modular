<?php

namespace Modules\User\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Exception;
use Modules\RolePermission\app\Models\Permission;
use Modules\User\app\Http\Requests\Api\V1\AdminPanel\User\ChangeUserRoleRequest;
use Modules\User\app\Http\Requests\Api\V1\AdminPanel\User\UserStoreRequest;
use Modules\User\app\Http\Requests\Api\V1\AdminPanel\User\UserUpdateRequest;
use Modules\User\app\Models\User;
use Modules\User\app\Resources\V1\AdminPanel\UserCollection;
use Modules\User\app\Resources\V1\AdminPanel\UserDetailResource;
use Modules\User\app\Services\UserService;

class UserController extends Controller
{
    protected string $permissionPrefix = 'admin_panel.user';

    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);

        $users = User::query()
            ->notSuperAdmin()
            ->when($request->search, function ($q, $v) {
                $q->whereLike(['first_name', 'last_name', 'phone',], $v);
            })
            ->when($request->registered, function ($q, $v) {
                $q->where('is_registered', $v == 'true');
            })
            ->when($request->admin, function ($q, $v) {
                $q->where('is_admin', $v == 'true');
            })
            ->when($request->status, function ($q, $v) {
                $q->where('status', $v == 'true');
            })
            ->orderBy('created_at', 'desc');

        $users = $request->get('paginate', 'true') == 'true'
            ? $users->paginate($request->get('page_size'))
            : $users->get();

        return response()->list(UserCollection::make($users)->response()->getData(true));
    }

    public function store(UserStoreRequest $request, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.create"]);
//        try {
            $user = $service->create($request->getSafeData());

            return response()->success(
                message: __('messages.store.success', ['attribute' => $service->getAlias(),]),
                data: UserDetailResource::make($user)
            );
//        } catch (Exception $e) {
//            return response()->error($e, __('messages.store.failure', ['attribute' => $service->getAlias(),]));
//        }
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.view"]);
        $this->authorize('view', $user);

        return response()->success(data: UserDetailResource::make($user));
    }

    public function update(UserUpdateRequest $request, User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.update"]);
        $this->authorize('update', $user);

        try {
            $user = $service->update($user, $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: UserDetailResource::make($user)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function destroy(User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.delete"]);
        $this->authorize('delete', $user);

        try {
            $service->delete($user);

            return response()->success(
                message: __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.delete.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function roleChange(ChangeUserRoleRequest $request, User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.role.attach']);
        $this->authorize('changeRole', $user);

        try {
            $service->changeRole($user, $request->roles);

            return response()->success(
                message: __('messages.role-change.success', ['attribute' => $service->getAlias(),]),
                data: UserDetailResource::make($user)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.role-change.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function statusChange(User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, "$this->permissionPrefix.change-status"]);
        $this->authorize('changeStatus', $user);

        try {
            $user = $service->changeStatus($user, !$user->status->value);

            return response()->success(
                message: __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $user->status->getAlias(),
                ]),
                data: UserDetailResource::make($user)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.status-change.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
