<?php

namespace Bespin\DataValidation\Vat\Countries;

class Slovakia implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // SK 9999999999
        if (preg_match('/^(SK)(\d{10})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // SLOVAKIA  SK + 10 digits
    // Checksum: the full 10-digit number must be divisible by 11.
    // Additional structural rules:
    //   - First digit must not be 0
    //   - Digits 3–10 must not all be zero (i.e. not just a country prefix + 00000000)
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^SK([1-9]\d{9})$/', $vatId, $m)) {
            return false;
        }
        $digits = $m[1];

        // The trailing 8 digits must be non-zero
        if ((int)substr($digits, 2) === 0) {
            return false;
        }

        return (int)$digits % 11 === 0;
    }
}