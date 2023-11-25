<?php

$CURRENCY = env('CURRENCY', 'T');

return [
    'sms_provider_name' =>env('SMS_PROVIDER_NAME', ''),
    'sms_provider_api_key' =>env('SMS_PROVIDER_API_KEY', ''),

    'currency' => $CURRENCY,
    'min_payable_amount' => $CURRENCY == 'T' ? '100' : '1000',

    'auth_token_type' => env('AUTH_TOKEN_TYPE', 'Bearer'),
    'token_expiration' => (int)env('TOKEN_EXPIRATION', 1) * 24 * 3600,
    'refresh_token_expiration' => (int)env('REFRESH_TOKEN_EXPIRATION', 1) * 24 * 3600,
    'personal_access_token_expiration' => (int)env('PERSONAL_ACCESS_TOKEN_EXPIRATION', 1) * 24 * 3600,
];
