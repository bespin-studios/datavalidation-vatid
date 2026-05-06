<?php

namespace Bespin\DataValidation\Vat\Countries;

class Germany implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // DE 999 999 999
        if (preg_match('/^(DE)(\d{3})(\d{3})(\d{3})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3].' '.$m[4];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // GERMANY  DE + 9 digits (first digit != 0)
    // Checksum: ISO 7064 MOD 11-10
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^DE([1-9]\d{8})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $check   = array_pop($d);
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