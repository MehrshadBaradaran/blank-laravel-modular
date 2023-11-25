<?php

namespace Modules\Notification\app\Enums;

enum NotificationInformTypeEnum: string
{
    case SUCCESS = 'success';
    case INFO    = 'info';
    case WARNING = 'warning';
    case ERROR   = 'error';

    public static function getNames(): array
    {
        return collect(self::cases())->pluck('name')->toArray();
    }

    public static function getValues(): array
    {
        return collect(self::cases())->pluck('value')->toArray();
    }

    public static function getDefaultCase(): self
    {
        return self::INFO;
    }

    public static function getDefaultCaseName(): string
    {
        return self::INFO->name;
    }

    public static function getDefaultCaseValue(): string
    {
        return self::INFO->value;
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
        $key = 'notification::aliases.inform-type.';

        return match ($this) {
            self::SUCCESS => __($key . self::SUCCESS->value),
            self::INFO    => __($key . self::INFO->value),
            self::WARNING => __($key . self::WARNING->value),
            self::ERROR   => __($key . self::ERROR->value),
        };
    }
}
