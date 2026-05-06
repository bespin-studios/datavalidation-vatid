<?php

namespace Bespin\DataValidation\Vat\Countries;

class Croatia implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // HR 12345678901
        if (preg_match('/^(HR)(\d{11})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // CROATIA  HR + 11 digits
    // ISO 7064 MOD 11-10 (same algorithm as DE Tax-ID: Mod 11,10)
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^HR(\d{11})$/', $vatId, $m)) {
            return false;
        }
        $d     = array_map('intval', str_split($m[1]));
        $check = array_pop($d);
        // ISO 7064 MOD 11-10
        $product = 10;
        foreach ($d as $digit) {
            $sum = ($digit + $product) % 10;
            if ($sum === 0) {
                $sum = 10;
            }
            $product = ($sum * 2) % 11;
        }
        $calculated = (11 - $product) % 10;
        return $calculated === $check;
    }
}