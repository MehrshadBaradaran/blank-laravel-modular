<?php

namespace App\Enums;

enum StatusEnum: int
{
    case FALSE = 0;
    case TRUE = 1;

    public static function getNames(): array
    {
        return collect(self::cases())->pluck('name')->toArray();
    }

    public static function getValues(): array
    {
        return collect(self::cases())->pluck('value')->toArray();
    }

    public static function getDefaultCase(): StatusEnum
    {
        return self::TRUE;
    }

    public static function getDefaultCaseName(): string
    {
        return self::TRUE->name;
    }

    public static function getDefaultCaseValue(): int
    {
        return self::TRUE->value;
    }

    public static function getDatabaseColumnComment(): string
    {
        $comment = '';

        foreach (self::cases() as $key => $case) {
            $comment .= "$key=>{$case->getText()}, ";
        }

        return str($comment)->replaceLast(', ', '');
    }

    public function getBoolValue(): bool
    {
        return $this == self::TRUE;
    }

    public function getText(): string
    {
        return match ($this) {
            self::TRUE => 'active',
            self::FALSE => 'inactive',
        };
    }

    public function getAlias(): string
    {
        return match ($this) {
            self::TRUE => __('aliases.status.active'),
            self::FALSE => __('aliases.status.inactive'),
        };
    }
}
