<?php

namespace Bespin\DataValidation\Vat\Countries;

class UnitedKingdom implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // GB 999 9999 99
        if (preg_match('/^(GB)(\d{3})(\d{4})(\d{2})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3].' '.$m[4];
        }
        // GB 999 9999 99 999  (branch traders)
        if (preg_match('/^(GB)(\d{3})(\d{4})(\d{2})(\d{3})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3].' '.$m[4].' '.$m[5];
        }
        // GB GD 999 / HA 999
        if (preg_match('/^(GB)(GD|HA)(\d{3})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // UNITED KINGDOM  GB + one of:
    //   9 digits        → standard traders
    //   12 digits       → branch traders (first 9 validated as standard)
    //   GD + 3 digits   → government departments (000-499)
    //   HA + 3 digits   → health authorities (500-999)
    //
    // Standard checksum: weights 8..2 on d1–d7; check = two-digit d8d9.
    //   Algorithm 1 (pre-2010):  check = 97 − (sum % 97)
    //   Algorithm 2 (post-2010): check = 97 − ((sum + 55) % 97)
    //   A number is valid if either algorithm matches.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        // Government departments: GD + 3 digits, value 000-499
        if (preg_match('/^GBGD(\d{3})$/', $vatId, $m)) {
            return (int)$m[1] <= 499;
        }
        // Health authorities: HA + 3 digits, value 500-999
        if (preg_match('/^GBHA(\d{3})$/', $vatId, $m)) {
            return (int)$m[1] >= 500;
        }
        // Standard 9-digit
        if (preg_match('/^GB(\d{9})$/', $vatId, $m)) {
            return self::verifyStandard($m[1]);
        }
        // Branch traders 12-digit (validate first 9 only)
        if (preg_match('/^GB(\d{12})$/', $vatId, $m)) {
            return self::verifyStandard(substr($m[1], 0, 9));
        }
        return false;
    }

    private static function verifyStandard(string $nineDigits): bool
    {
        $d       = array_map('intval', str_split($nineDigits));
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = $d[7] * 10 + $d[8];

        // Algorithm 1 (pre-2010 numbers)
        if ((97 - ($sum % 97)) === $check) {
            return true;
        }
        // Algorithm 2 (post-2010 numbers)
        return (97 - (($sum + 55) % 97)) === $check;
    }
}