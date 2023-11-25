<?php

namespace Modules\User\app\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
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

            return response()->json([
                'message' => __('messages.update.success', ['attribute' => $service->getAlias(),]),
                'data' => new UserResource($user),
            ]);

        } catch (Exception $exception) {
            Log::channel('bug_report')->error('User update: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }

    public function passwordUpdate(PasswordUpdateRequest $request, UserService $service): JsonResponse
    {
        try {
            $service->updatePassword(Auth::user(), $request->getSafeData());

            return response()->json([
                'message' => __('messages.password-update.success', ['attribute' => $service->getAlias(),]),
            ]);

        } catch (Exception $exception) {
            Log::channel('bug_report')->error('User update password: ' . $exception->getMessage());
            return response()->json([
                'message' => __('messages.password-update.failure', ['attribute' => $service->getAlias(),]),
            ], 500);
        }
    }
}
