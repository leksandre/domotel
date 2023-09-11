<?php

declare(strict_types=1);

namespace Kelnik\Core\Helpers;

final class NumberHelper
{
    public static function normalizeFloat(float $val): float
    {
        $fraction = explode('.', (string)($val - (int)$val));
        $fraction = $fraction[1] ?? false;

        if (!$fraction) {
            return $val;
        }

        $originLength = strlen($fraction);
        $offset = 0;

        for ($i = $originLength; $i > 0; $i--) {
            if (substr($fraction, -$i) !== '0') {
                break;
            }
        }

        return round($val, $originLength - $offset);
    }

    public static function prepareNumeric(string $value): string
    {
        return preg_replace('![^\d.,\-]!i', '', $value);
    }

    /**
     * @param string[]|int[] $nums
     *
     * @return array
     */
    public static function filterInteger(array $nums): array
    {
        if (!$nums) {
            return $nums;
        }

        foreach ($nums as $k => &$v) {
            $v = (int)$v;
            if (!$v) {
                unset($nums[$k]);
            }
        }
        unset($k, $v);

        return $nums;
    }

    public static function arabicToRoman(int $number): string
    {
        $res = '';
        $roman = [
            'M' => 1000,
            'CM' => 900,
            'D' => 500,
            'CD' => 400,
            'C' => 100,
            'XC' => 90,
            'L' => 50,
            'XL' => 40,
            'X' => 10,
            'IX' => 9,
            'V' => 5,
            'IV' => 4,
            'I' => 1
        ];

        foreach ($roman as $glyph => $val) {
            if ($number <= 0) {
                break;
            }

            while ($number >= $val) {
                $res .= $glyph;
                $number -= $val;
            }
        }

        return $res;
    }
}
