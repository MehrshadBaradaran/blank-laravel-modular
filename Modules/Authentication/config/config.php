<?php

return [
    'name' => 'Authentication',

    'otp_expiration_seconds' => env('OTP_EXPIRATION_MINUTES', 2) * 60,
    'otp_length' => env('OTP_LENGTH', 5),
];
