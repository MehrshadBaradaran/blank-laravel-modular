<?php

namespace Modules\Authentication\app\Services;

use Modules\Authentication\app\Models\VerificationToken;
use Modules\User\app\Models\User;

class VerificationTokenService
{
    public function getLatestTokenByPhone(string|null $phone): ?VerificationToken
    {
        $user = User::active()
            ->where('phone', $phone)
            ->first();

        return VerificationToken::query()
            ->where('user_id', $user->id)
            ->latestCode()
            ->first();
    }

    public function getLatestOTPByPhone(string|null $phone): ?string
    {
        return $this->getLatestTokenByPhone($phone)?->otp;
    }
}
