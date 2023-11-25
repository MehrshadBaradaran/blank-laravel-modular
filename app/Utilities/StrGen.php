<?php

namespace App\Utilities;

use Illuminate\Support\Facades\DB;
use function PHPUnit\Framework\stringStartsWith;

class StrGen
{
    const FULL_STRING = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    const UPPERCASE_LETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const LOWERCASE_LETTERS = 'abcdefghijklmnopqrstuvwxyz';
    const NUMBERS = '0123456789';
    protected static string $characters = '';
    protected static array $processes = [];

    // Generate string with saved processes respectively
    protected static function generateBySavedProcesses(): string
    {
        $str = '';
        foreach (static::$processes as $process => $length) {
            switch ($process) {
                case 'uppercase':
                {
                    $str .= self::generate(self::UPPERCASE_LETTERS, $length);
                    break;
                }
                case 'lowercase':
                {
                    $str .= self::generate(self::LOWERCASE_LETTERS, $length);
                    break;
                }
                case 'number':
                {
                    $str .= self::generate(self::NUMBERS, $length);
                    break;
                }
                default:
                {
                    $str .= self::generate(self::FULL_STRING, $length);
                    break;
                }
            }
        }
        return $str;
    }

    protected static function generate(string $chars, int $length): string
    {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[rand(0, strlen($chars) - 1)];
        }
        return $str;
    }

    public static function uppercase(int $length): static
    {
        static::$characters .= self::generate(self::UPPERCASE_LETTERS, $length);
        static::$processes['uppercase'] = $length;
        return new static;
    }

    public static function lowercase(int $length): static
    {
        static::$characters .= self::generate(self::LOWERCASE_LETTERS, $length);
        static::$processes['lowercase'] = $length;
        return new static;
    }

    public static function number(int $length): static
    {
        do {
            $str = self::generate(self::NUMBERS, $length);
        } while ($str[0] == '0');

        static::$characters .= $str;
        static::$processes['number'] = $length;
        return new static;
    }

    public static function unique(string $table, string $column): static
    {
        $records = DB::table($table)->get()->map(function ($record) use ($column) {
            return $record->$column;
        })->toArray();

        if (DB::table($table)->where($column, static::$characters)->exists()) {
            do {
                static::$characters = $str = self::generateBySavedProcesses();
            } while (in_array($str, $records));
        }

        return new static;
    }

    public static function get(): string
    {
        $result = static::$characters;
        static::$characters = '';
        return $result;
    }

    public static function randomUnique(string $table, string $column, int $length = 4): string
    {
        $records = DB::table($table)->get()->map(function ($record) use ($column) {
            return $record->$column;
        })->toArray();

        do {
            $string = self::generate(self::FULL_STRING, $length);
        } while (in_array($string, $records));

        return $string;
    }
}
