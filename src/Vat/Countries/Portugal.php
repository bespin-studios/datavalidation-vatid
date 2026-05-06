<?php

namespace Bespin\DataValidation\Vat\Countries;

class Portugal implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // PT 999 999 999
        if (preg_match('/^(PT)(\d{3})(\d{3})(\d{3})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3].' '.$m[4];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // PORTUGAL  PT + 9 digits
    // Checksum: weights 9..2 on digits 1-8; check = 11 - (sum mod 11),
    // if result >= 10 check digit must be 0.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^PT(\d{9})$/', $vatId, $m)) {
            return false;
        }
        $d   = array_map('intval', str_split($m[1]));
        $sum = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $d[$i] * (9 - $i);
        }
        $check = 11 - ($sum % 11);
        if ($check >= 10) {
            $check = 0;
        }
        return $check === $d[8];
    }
}