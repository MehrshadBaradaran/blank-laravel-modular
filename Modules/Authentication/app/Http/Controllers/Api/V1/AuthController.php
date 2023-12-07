<?php

namespace Modules\Authentication\app\Http\Controllers\Api\V1;

use App\Utilities\SMS;
use App\Utilities\StrGen;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Authentication\app\Http\Requests\Api\V1\GetOTPRequest;
use Modules\Authentication\app\Http\Requests\Api\V1\LoginOTPRequest;
use Modules\Authentication\app\Http\Requests\Api\V1\LoginPasswordRequest;
use Modules\Authentication\app\Http\Requests\Api\V1\PassResetRequest;
use Modules\Authentication\app\Http\Requests\Api\V1\PhoneStatusRequest;
use Modules\Authentication\app\Http\Requests\Api\V1\PhoneVerifyRequest;
use Modules\Authentication\app\Http\Requests\Api\V1\RegisterRequest;
use Modules\Authentication\app\Resources\V1\AuthUserResource;
use Modules\Authentication\app\Services\AuthService;
use Modules\User\app\Models\User;

class AuthController extends Controller
{
    public function user(): JsonResponse
    {
        return response()->success(data: [
            'user' => AuthUserResource::make(Auth::user()),
        ]);
    }

    public function phoneStatus(PhoneStatusRequest $request): JsonResponse
    {
        try {
            $user = User::where('phone', $request->phone)->first();
            if (!$user) {
                $user = User::create([
                    'phone' => $request->phone,
                    'password' => 'password',
                ]);
            }

            return response()->success(data: [
                'should_register' => !$user->is_registered,
            ]);

        } catch (Exception $exception) {
            return response()->error($exception, __('errors.500'));
        }
    }

    public function phoneVerify(PhoneVerifyRequest $request): JsonResponse
    {
        try {
            $user = User::where('phone', $request->phone)->first();

            $user->update([
                'phone_verified_at' => now()
            ]);

            return response()->success(data: [
                'should_register' => !$user->is_registered,
            ]);

        } catch (Exception $exception) {
            return response()->error($exception, __('errors.500'));
        }
    }

    public function loginWithPassword(LoginPasswordRequest $request, AuthService $authService): JsonResponse
    {
        try {
            if (Auth::attempt($request->safe()->all())) {
                $user = User::where('phone', $request->phone)->first();

                $accessToken = $authService->generateAccessToken($user);

                return response()->success(data: [
                    'token' => $accessToken->token,
                    'token_type' => $accessToken->token_type,
                    'token_expiration_seconds' => $accessToken->token_expiration_seconds,
                    'user' => new AuthUserResource($user),
                ]);
            }
            return response()->error(message: __('auth.wrong-credentials.phone'), statusCode: 401);

        } catch (Exception $exception) {
            return response()->error($exception, __('errors.500'));
        }
    }

    public function getOTP(GetOTPRequest $request): JsonResponse
    {
        try {
            $otpLength = config('config.otp_length');
            $otpExpiration = config('config.otp_expiration_seconds');

            $user = User::where('phone', $request->phone)->first();
            $verificationToken = $user->verificationTokens()
                ->where('user_id', $user->id)
                ->latestCode()
                ->first();


            if (!$verificationToken) {
                $otp = StrGen::number($otpLength)->get();
                $user->verificationTokens()->create(['otp' => $otp,]);
                SMS::sendPattern(['phone' => $request->phone, 'token1' => $otp, 'template' => 'verify']);
            }

            return response()->success(__('messages.login.otp-send', ['phone' => $user->phone_with_zero,]),
                data: [
                    'otp_expiration_seconds' => $otpExpiration,
                    'otp_length' => $otpLength,
                ]);

        } catch (Exception $exception) {
            return response()->error($exception, __('errors.500'));
        }
    }

    public function loginWithOTP(LoginOTPRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $user = User::where('phone', $request->phone)->first();

            $user->verificationTokens()->temporary()->delete();

            $accessToken = $authService->generateAccessToken($user);

            return response()->success(data: [
                'token' => $accessToken->token,
                'token_type' => $accessToken->token_type,
                'token_expiration_seconds' => $accessToken->token_expiration_seconds,
                'user' => new AuthUserResource($user),
            ]);

        } catch (Exception $exception) {
            return response()->error($exception, __('errors.500'));
        }
    }

    public function register(RegisterRequest $request, AuthService $authService): JsonResponse
    {
        try {
            $response = DB::transaction(function () use ($request, $authService) {
                $user = User::where('phone', $request->phone)->first();
                $user->update($request->except('password_confirmation'));

                $accessToken = $authService->generateAccessToken($user);

                return response()->success(__('messages.registration.success'), data: [
                    'token' => $accessToken->token,
                    'token_type' => $accessToken->token_type,
                    'token_expiration_seconds' => $accessToken->token_expiration_seconds,
                    'user' => new AuthUserResource($user),
                ]);

            });
            return response()->json($response);

        } catch (Exception $exception) {
            return response()->error($exception, __('messages.registration.failure'));
        }
    }

    public function passwordReset(PassResetRequest $request): JsonResponse
    {
        try {
            $user = User::where('phone', $request->phone)->first();

            $user->update([
                'password' => $request->password,
            ]);

            $user->verificationTokens()->delete();

            return response()->success(__('passwords.reset'));

        } catch (Exception $exception) {
            return response()->error($exception, __('errors.500'));
        }
    }

    public function logout(): JsonResponse
    {
        try {
            Auth::user()->tokens()->delete();

            return response()->success();

        } catch (Exception $exception) {
            return response()->error($exception, __('errors.500'));
        }
    }
}
