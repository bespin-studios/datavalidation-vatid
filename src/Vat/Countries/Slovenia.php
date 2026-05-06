<?php

namespace Bespin\DataValidation\Vat\Countries;

class Slovenia implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // SI 99999999
        if (preg_match('/^(SI)(\d{8})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // SLOVENIA  SI + 8 digits (first digit 1-9)
    // Checksum: weights 8,7,6,5,4,3,2 on digits 1-7;
    // check = 11 - (sum mod 11); if result is 10 or 11 → invalid.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^SI([1-9]\d{7})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = 11 - ($sum % 11);
        if ($check === 10 || $check === 11) {
            return false;
        }
        return $check === $d[7];
    }
}