<?php

namespace Bespin\DataValidation\Vat\Countries;

class Lithuania implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // LT 999999999 (9 digits) or LT 999999999999 (12 digits)
        if (preg_match('/^(LT)(\d{9}|\d{12})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // LITHUANIA  LT + 9 or 12 digits
    //
    // 9 digits → legal entities
    // 12 digits → temporarily registered taxable persons (d5 must be 1)
    //
    // Checksum (applied to first 8 digits for 9-digit, first 11 for 12-digit):
    //   Primary weights:     1,2,3,4,5,6,7,8[,9,1,2]
    //   If remainder = 10:
    //   Alternative weights: 3,4,5,6,7,8,9,1[,2,3,4]
    //   If still 10: check = 0
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (preg_match('/^LT(\d{9})$/', $vatId, $m)) {
            return self::verifyChecksum($m[1], 9);
        }
        if (preg_match('/^LT(\d{12})$/', $vatId, $m)) {
            // 5th digit must be 1 for temporarily registered entities
            if ($m[1][4] !== '1') {
                return false;
            }
            return self::verifyChecksum($m[1], 12);
        }
        return false;
    }

    private static function verifyChecksum(string $digits, int $length): bool
    {
        $d            = array_map('intval', str_split($digits));
        $checkPos     = $length - 1;
        $weightLength = $length - 1;

        // Primary weights cycle: 1,2,3,4,5,6,7,8,9,1,2,...
        $sum = 0;
        for ($i = 0; $i < $weightLength; $i++) {
            $sum += $d[$i] * (($i % 9) + 1);
        }
        $remainder = $sum % 11;

        if ($remainder !== 10) {
            return ($remainder % 10) === $d[$checkPos];
        }

        // Alternative weights cycle: 3,4,5,6,7,8,9,1,2,3,4,...
        $sum = 0;
        for ($i = 0; $i < $weightLength; $i++) {
            $sum += $d[$i] * ((($i + 2) % 9) + 1);
        }
        $remainder = $sum % 11;
        $check     = $remainder === 10 ? 0 : $remainder;

        return $check === $d[$checkPos];
    }
}