<?php

namespace Bespin\DataValidation\Vat\Countries;

class Belgium implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // BE 0999 999 999
        if (preg_match('/^(BE)(\d{4})(\d{3})(\d{3})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3].' '.$m[4];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // BELGIUM  BE + 10 digits (leading 0 or 1)
    // Checksum: last two digits = 97 - (first 8 digits mod 97)
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^BE([01]\d{9})$/', $vatId, $m)) {
            return false;
        }
        $digits = $m[1];
        $base   = (int)substr($digits, 0, 8);
        $check  = (int)substr($digits, 8, 2);
        return $check === (97 - ($base % 97));
    }
}