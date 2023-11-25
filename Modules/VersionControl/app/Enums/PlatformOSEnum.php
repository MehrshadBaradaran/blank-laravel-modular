<?php

namespace Modules\VersionControl\app\Enums;

enum PlatformOSEnum: string
{
    case ANDROID      = 'android';
    case WINDOWS      = 'windows';
    case WINDOWS_PONE = 'windows_phone';
    case IOS          = 'ios';
    case MAC_OS       = 'macOS';
    case LINUX        = 'linux';

    public static function getNames(): array
    {
        return collect(self::cases())->pluck('name')->toArray();
    }

    public static function getValues(): array
    {
        return collect(self::cases())->pluck('value')->toArray();
    }

    public static function getDefaultCase(): PlatformOSEnum
    {
        return self::ANDROID;
    }

    public static function getDefaultCaseName(): string
    {
        return self::ANDROID->name;
    }

    public static function getDefaultCaseValue(): string
    {
        return self::ANDROID->value;
    }

    public static function getDatabaseColumnComment(): string
    {
        $comment = '';

        foreach (self::cases() as $key => $case) {
            $comment .= "$key=>$case->value, ";
        }

        return str($comment)->replaceLast(', ', '');
    }

    public function getAlias(): string
    {
        $key = 'versioncontrol::aliases.os.';

        return match ($this) {
            self::ANDROID      => __($key . self::ANDROID->value),
            self::WINDOWS      => __($key . self::WINDOWS->value),
            self::WINDOWS_PONE => __($key . self::WINDOWS_PONE->value),
            self::MAC_OS       => __($key . self::MAC_OS->value),
            self::IOS          => __($key . self::IOS->value),
            self::LINUX        => __($key . self::LINUX->value),
        };
    }
}
