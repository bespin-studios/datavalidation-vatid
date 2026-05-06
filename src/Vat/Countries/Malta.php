<?php

namespace Bespin\DataValidation\Vat\Countries;

class Malta implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // MT 99999999
        if (preg_match('/^(MT)(\d{8})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // MALTA  MT + 8 digits
    // Checksum: weights 3,4,6,7,8,9 on d1–d6;
    // check (two digits d7d8) = 37 - (sum % 37).
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^MT(\d{8})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [3, 4, 6, 7, 8, 9];
        $sum     = 0;
        for ($i = 0; $i < 6; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = 37 - ($sum % 37);
        return $check === ($d[6] * 10 + $d[7]);
    }
}