<?php

namespace Bespin\DataValidation\Vat\Countries;

class Denmark implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // DK 9999 9999
        if (preg_match('/^(DK)(\d{4})(\d{4})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // DENMARK  DK + 8 digits
    // Checksum: weights 2,7,6,5,4,3,2,1; sum divisible by 11
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^DK(\d{8})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [2, 7, 6, 5, 4, 3, 2, 1];
        $sum     = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        return $sum % 11 === 0;
    }
}