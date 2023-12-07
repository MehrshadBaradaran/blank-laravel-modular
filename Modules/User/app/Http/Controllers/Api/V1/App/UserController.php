<?php

namespace Modules\User\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Exception;
use Modules\User\app\Http\Requests\Api\V1\App\User\PasswordUpdateRequest;
use Modules\User\app\Http\Requests\Api\V1\App\User\UserUpdateRequest;
use Modules\User\app\Resources\V1\App\UserResource;
use Modules\User\app\Services\UserService;

class UserController extends Controller
{
    public function update(UserUpdateRequest $request, UserService $service): JsonResponse
    {
        try {
            $user = $service->update(Auth::user(), $request->getSafeData());

            return response()->success(
                message: __('messages.update.success', ['attribute' => $service->getAlias(),]),
                data: UserResource::make($user)
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.update.failure', ['attribute' => $service->getAlias(),]));
        }
    }

    public function passwordUpdate(PasswordUpdateRequest $request, UserService $service): JsonResponse
    {
        try {
            $service->updatePassword(Auth::user(), $request->getSafeData());

            return response()->success(
                message: __('messages.password-update.success', ['attribute' => $service->getAlias(),]),
                data: UserResource::make(Auth::user())
            );
        } catch (Exception $e) {
            return response()->error($e, __('messages.password-update.failure', ['attribute' => $service->getAlias(),]));
        }
    }
}
