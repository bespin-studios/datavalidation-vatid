<?php

namespace Bespin\DataValidation\Vat\Countries;

class Sweden implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // SE 999999999901
        if (preg_match('/^(SE)(\d{10})(01)$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // SWEDEN  SE + 12 digits (last two are always 01 for single-entity companies)
    // Checksum: Luhn algorithm on first 10 digits; digit 11 is type (01 suffix).
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^SE(\d{10}01)$/', $vatId, $m)) {
            return false;
        }
        $d = array_map('intval', str_split($m[1]));
        // Luhn on first 10 digits
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            if ($i % 2 === 0) {
                $p   = $d[$i] * 2;
                $sum += $p > 9 ? $p - 9 : $p;
            } else {
                $sum += $d[$i];
            }
        }
        return $sum % 10 === 0;
    }
}