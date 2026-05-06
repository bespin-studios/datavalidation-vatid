<?php

namespace Bespin\DataValidation\Vat\Countries;

class Luxembourg implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // LU 9999 9999
        if (preg_match('/^(LU)(\d{4})(\d{4})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // LUXEMBOURG  LU + 8 digits
    // Checksum: first 6 digits mod 89; last two digits are the check
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^LU(\d{8})$/', $vatId, $m)) {
            return false;
        }
        $base  = (int)substr($m[1], 0, 6);
        $check = (int)substr($m[1], 6, 2);
        return $check === ($base % 89);
    }
}