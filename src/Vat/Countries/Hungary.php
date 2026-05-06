<?php

namespace Bespin\DataValidation\Vat\Countries;

class Hungary implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // HU 99999999
        if (preg_match('/^(HU)(\d{8})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // HUNGARY  HU + 8 digits
    // Checksum: weights 9,7,3,1,9,7,3 on d1–d7;
    // check = (10 - sum % 10) % 10; compare with d8.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^HU(\d{8})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [9, 7, 3, 1, 9, 7, 3];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = (10 - ($sum % 10)) % 10;
        return $check === $d[7];
    }
}