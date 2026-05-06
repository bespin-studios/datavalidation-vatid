<?php

namespace Bespin\DataValidation\Vat\Countries;

class Finland implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // FI 9999999 9
        if (preg_match('/^(FI)(\d{7})(\d{1})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // FINLAND  FI + 8 digits
    // Checksum: weights 7,9,10,5,8,4,2 on digits 1-7; check = (11 - sum%11) %11
    // if result is 10 the number is invalid.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^FI(\d{8})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [7, 9, 10, 5, 8, 4, 2];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        if ($remainder === 0) {
            $check = 0;
        } elseif ($remainder === 1) {
            return false; // invalid
        } else {
            $check = 11 - $remainder;
        }
        return $check === $d[7];
    }
}