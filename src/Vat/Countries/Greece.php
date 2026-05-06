<?php

namespace Bespin\DataValidation\Vat\Countries;

class Greece implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // EL 999999999  (Greece uses EL, not GR, for VAT)
        if (preg_match('/^(EL)(\d{9})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // GREECE  EL + 9 digits  (note: prefix is EL, not GR)
    // Checksum: weights 256,128,64,32,16,8,4,2 on d1–d8;
    // check = sum % 11 % 10; compare with d9.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^EL(\d{9})$/', $vatId, $m)) {
            return false;
        }
        $d       = array_map('intval', str_split($m[1]));
        $weights = [256, 128, 64, 32, 16, 8, 4, 2];
        $sum     = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = ($sum % 11) % 10;
        return $check === $d[8];
    }
}