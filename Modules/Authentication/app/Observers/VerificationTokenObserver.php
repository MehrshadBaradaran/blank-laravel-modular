<?php

namespace Modules\Authentication\app\Observers;


use Modules\Authentication\app\Models\VerificationToken;

class VerificationTokenObserver
{
    public function creating(VerificationToken $token): void
    {
        $token->expire_at = now()->addSeconds(config('config.otp_expiration_seconds'));
    }
}
