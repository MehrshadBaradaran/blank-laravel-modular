<?php

namespace Modules\Gallery\app\Enums;

enum GallerySectionEnum: string
{
    case DEFAULT   = 'default';
    case USER      = 'user';
    case BANNER    = 'banner';
    case PLATFORM  = 'platform';

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
        return self::DEFAULT;
    }

    public static function getDefaultCaseName(): string
    {
        return self::DEFAULT->name;
    }

    public static function getDefaultCaseValue(): string
    {
        return self::DEFAULT->value;
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
        $key = 'gallery::aliases.section.';

        return match ($this) {
            self::DEFAULT   => __($key . self::DEFAULT->value),
            self::USER      => __($key . self::USER->value),
            self::BANNER    => __($key . self::BANNER->value),
            self::PLATFORM  => __($key . self::PLATFORM->value),
        };
    }
}
