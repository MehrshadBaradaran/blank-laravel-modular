<?php

namespace Modules\User\app\Http\Controllers\Api\V1\AdminPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
    public function index(Request $request): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.user.view']);

        $users = User::query()
            ->notSuperAdmin()
            ->when($request->search, function ($q, $value) {
                $q->where(function ($q) use ($value) {
                    $q->where('first_name', 'LIKE', "%$value%")
                        ->orWhere('last_name', 'LIKE', "%$value%")
                        ->orWhere('phone', 'LIKE', "%$value%");
                });
            })
            ->when($request->registered, function ($q, $value) {
                $q->where('is_registered', $value == 'true');
            })
            ->when($request->admin, function ($q, $value) {
                $q->where('is_admin', $value == 'true');
            })
            ->when($request->status, function ($q, $value) {
                $q->where('status', $value == 'true');
            })
            ->orderBy('created_at', 'desc');

        $users = $request->get('paginate', 'true') == 'true'
            ? $users->paginate($request->get('page_size'))
            : $users->get();

        return response()->json((new UserCollection($users))->response()->getData(true));
    }

    public function store(UserStoreRequest $request, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.user.create']);

        try {
            $user = $service->create($request->getSafeData());

            return response()->json([
                'message' => __('messages.store.success', ['attribute' => $service->getAlias(),]),
                'data' => new UserDetailResource($user),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('User store: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.store.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function show(User $user): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.user.view']);
        $this->authorize('view', $user);

        return response()->json([
            'data' => new UserDetailResource($user),
        ]);
    }

    public function update(UserUpdateRequest $request, User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.user.update']);
        $this->authorize('update', $user);

        try {
            $user = $service->update($user, $request->getSafeData());

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new UserDetailResource($user),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('User update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function destroy(User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.user.delete']);
        $this->authorize('delete', $user);

        try {
            $service->delete($user);

            return response()->json([
                'message' => __('messages.delete.success', ['attribute' => $service->getAlias(),]),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('User delete: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.delete.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function roleChange(ChangeUserRoleRequest $request, User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.role.attach']);
        $this->authorize('changeRole', $user);

        try {
            $service->changeRole($user, $request->roles);

            return response()->json([
                'message' => __('messages.role-change.success', ['attribute' => $service->getAlias()]),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('User change role: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.role-change.failure', ['attribute' => $service->getAlias()]),
            ], 500);
        }
    }

    public function statusChange(User $user, UserService $service): JsonResponse
    {
        $this->authorize('checkPermission', [Permission::class, 'admin_panel.user.change-status']);
        $this->authorize('changeStatus', $user);

        try {
            $user = $service->changeStatus($user, !$user->status->value);

            return response()->json([
                'message' => __('messages.status-change.success', [
                    'attribute' => $service->getAlias(),
                    'status' => $user->status->getAlias(),
                ]),
            ]);

        } catch (Exception $exception) {
            Log::channel('report')->error('User change status: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.status-change.failure'),
            ], 500);
        }
    }
}
