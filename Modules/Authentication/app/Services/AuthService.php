<?php

namespace Modules\Authentication\app\Services;

use Modules\User\app\Models\User;

class AuthService
{
    public function generateAccessToken(User $user): object
    {
        $user->tokens()->update(['revoked' => true,]);

        $user->update([
            'last_login' => now(),
        ]);

        return (object)[
            'token' => $user->createToken('auth_token')->accessToken,
            'token_type' => config('config.auth_token_type'),
            'token_expiration_seconds' => config('config.personal_access_token_expiration'),
        ];
    }
}
