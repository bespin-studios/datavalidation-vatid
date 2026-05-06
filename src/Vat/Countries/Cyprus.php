<?php

namespace Bespin\DataValidation\Vat\Countries;

class Cyprus implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // CY 99999999 L
        if (preg_match('/^(CY)(\d{8})([A-Z])$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // CYPRUS  CY + 8 digits + 1 letter
    // Odd-position digits (1,3,5,7) mapped through a lookup table;
    // even-position digits (2,4,6,8) used as-is.
    // Sum mod 26 maps to check letter A–Z.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^CY(\d{8})([A-Z])$/', $vatId, $m)) {
            return false;
        }
        $d     = array_map('intval', str_split($m[1]));
        $check = $m[2];

        // Odd-position lookup (0-indexed positions 0,2,4,6)
        $oddMap = [1, 0, 5, 7, 9, 13, 15, 17, 19, 21];
        $sum    = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += ($i % 2 === 0) ? $oddMap[$d[$i]] : $d[$i];
        }

        return $check === chr(ord('A') + ($sum % 26));
    }
}