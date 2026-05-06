<?php

namespace Bespin\DataValidation\Vat\Countries;

class Austria implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // ATU 12345678
        if (preg_match('/^(ATU)(\d{8})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // AUSTRIA  ATU + 8 digits
    // Checksum: weights 1,2,1,2,1,2,1 on digits 2–8, doubled digits > 9 are
    // summed by cross-sum (i.e. d - 9), result subtracted from 10 mod 10.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^ATU(\d{8})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [1, 2, 1, 2, 1, 2, 1];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $p   = $d[$i] * $weights[$i];
            $sum += $p > 9 ? $p - 9 : $p;
        }
        $check = (10 - ($sum + 4) % 10) % 10;
        return $check === $d[7];
    }
}