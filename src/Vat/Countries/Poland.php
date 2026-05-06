<?php

namespace Bespin\DataValidation\Vat\Countries;

class Poland implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // PL 999-999-99-99
        if (preg_match('/^(PL)(\d{3})(\d{3})(\d{2})(\d{2})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].'-'.$m[3].'-'.$m[4].'-'.$m[5];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // POLAND  PL + 10 digits
    // Checksum: weights 6,5,7,2,3,4,5,6,7 on digits 1-9; check = sum mod 11
    // if result is 10, the number is invalid.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^PL(\d{10})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [6, 5, 7, 2, 3, 4, 5, 6, 7];
        $sum     = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = $sum % 11;
        if ($check === 10) {
            return false;
        }
        return $check === $d[9];
    }
}