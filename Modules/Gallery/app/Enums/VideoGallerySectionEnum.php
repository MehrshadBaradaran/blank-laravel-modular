<?php

namespace Modules\Gallery\app\Enums;

enum VideoGallerySectionEnum: string
{
    case DEFAULT = 'default';

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
        $key = 'gallery::aliases.video.section.';

        return match ($this) {
            self::DEFAULT => __($key . self::DEFAULT->value),
        };
    }
}
