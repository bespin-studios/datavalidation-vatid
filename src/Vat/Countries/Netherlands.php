<?php

namespace Bespin\DataValidation\Vat\Countries;

class Netherlands implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // NL 999999999 B 01
        if (preg_match('/^(NL)(\d{9})(B\d{2})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // NETHERLANDS  NL + 9 digits + B + 2 digits
    // Checksum: MOD 11 on first 8 digits with weights 9..2; result must equal digit 9
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^NL(\d{9})B(\d{2})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [9, 8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = $sum % 11;
        if ($check > 9) {
            return false;
        }
        return $check === $d[8];
    }
}