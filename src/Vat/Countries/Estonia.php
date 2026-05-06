<?php

namespace Bespin\DataValidation\Vat\Countries;

class Estonia implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // EE 999999999
        if (preg_match('/^(EE)(\d{9})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // ESTONIA  EE + 9 digits
    // Checksum: weights 3,7,1,3,7,1,3,7 on d1–d8;
    // check = (10 - sum % 10) % 10; compare with d9.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^EE(\d{9})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [3, 7, 1, 3, 7, 1, 3, 7];
        $sum     = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = (10 - ($sum % 10)) % 10;
        return $check === $d[8];
    }
}