<?php

namespace Bespin\DataValidation\Vat\Countries;

class Romania implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // RO 99..99 (2–10 digits)
        if (preg_match('/^(RO)(\d{2,10})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // ROMANIA  RO + 2–10 digits
    // Checksum: weights 7,5,3,2,1,7,5,3,2 right-aligned against d1..d(n-1);
    // check = (sum * 10) % 11; if 10 → 0; compare with last digit.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^RO(\d{2,10})$/', $vatId, $m)) {
            return false;
        }
        $digits     = $m[1];
        $len        = strlen($digits);
        $d          = array_map('intval', str_split($digits));
        $allWeights = [7, 5, 3, 2, 1, 7, 5, 3, 2];

        // Right-align: use the last ($len - 1) weights
        $weights = array_slice($allWeights, -(($len - 1)));
        $sum     = 0;
        for ($i = 0; $i < $len - 1; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = ($sum * 10) % 11;
        if ($check === 10) {
            $check = 0;
        }
        return $check === $d[$len - 1];
    }
}