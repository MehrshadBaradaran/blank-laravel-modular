<?php

namespace App\Utilities;

use Kavenegar\Exceptions\ApiException;
use Kavenegar\Exceptions\HttpException;
use Kavenegar\KavenegarApi;

class SMS
{
    public static function sendPattern(array $data): bool|string
    {
        try {
            $sms = new KavenegarApi(config('config.kavenegar_api_key'));
            $sms->VerifyLookup(
                $data['phone'],
                $data['token1'] ?? null,
                $data['token2'] ?? null,
                $data['token3'] ?? null,
                $data['template']
            );
            return true;
        } catch (ApiException | HttpException $e) {
            return $e->getMessage();
        }
    }

    public static function provinceToArray(): array
    {
        $path = public_path('iran-province-city/json/provinces.json');
        return json_decode(file_get_contents($path));
    }

    public static function cityToArray(): array
    {
        $path = public_path('/iran-province-city/json/cities.json');
        return json_decode(file_get_contents($path));
    }
}

