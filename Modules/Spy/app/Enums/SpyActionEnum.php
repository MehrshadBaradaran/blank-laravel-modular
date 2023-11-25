<?php

namespace Modules\Spy\app\Enums;

enum SpyActionEnum: string
{
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case STATUS = 'status';
    case DELETE = 'delete';
    case ATTACH = 'attach';
    case OTHER = 'other';

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
        return self::OTHER;
    }

    public static function getDefaultCaseName(): string
    {
        return self::OTHER->name;
    }

    public static function getDefaultCaseValue(): string
    {
        return self::OTHER->value;
    }

    public static function getCaseByValue(string $value): self
    {
        return match ($value) {
            'view' => self::VIEW,
            'create' => self::CREATE,
            'update' => self::UPDATE,
            'status' => self::STATUS,
            'delete' => self::DELETE,
            'attach' => self::ATTACH,
            'other' => self::OTHER,
        };
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
        $aliasKey = 'spy::aliases.action.';

        return match ($this) {
            self::VIEW => __($aliasKey . self::VIEW->value),
            self::CREATE => __($aliasKey . self::CREATE->value),
            self::UPDATE => __($aliasKey . self::UPDATE->value),
            self::STATUS => __($aliasKey . self::STATUS->value),
            self::DELETE => __($aliasKey . self::DELETE->value),
            self::ATTACH => __($aliasKey . self::ATTACH->value),
            self::OTHER => __($aliasKey . self::OTHER->value),
        };
    }
}
