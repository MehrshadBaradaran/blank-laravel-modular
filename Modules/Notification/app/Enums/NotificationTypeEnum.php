<?php

namespace Modules\Notification\app\Enums;

enum NotificationTypeEnum: string
{
    case MANUAL = 'manual';
    case SYSTEM = 'system';

    public static function getNames(): array
    {
        return collect(self::cases())->pluck('name')->toArray();
    }

    public static function getValues(): array
    {
        return collect(self::cases())->pluck('value')->toArray();
    }

    public static function getDefaultCase(): NotificationTypeEnum
    {
        return self::MANUAL;
    }

    public static function getDefaultCaseName(): string
    {
        return self::MANUAL->name;
    }

    public static function getDefaultCaseValue(): string
    {
        return self::MANUAL->value;
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
        $key = 'notification::aliases.type.';

        return match ($this) {
            self::MANUAL => __($key . self::MANUAL->value),
            self::SYSTEM => __($key . self::SYSTEM->value),
        };
    }
}
