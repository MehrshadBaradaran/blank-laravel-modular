<?php

namespace Modules\Authentication\app\Observers;


use Modules\Auth\app\Models\VerificationToken;

class VerificationTokenObserver
{
    public function creating(VerificationToken $token)
    {
        $token->expire_at = now()->addSeconds(config('config.otp_expiration_seconds'));
    }
}
