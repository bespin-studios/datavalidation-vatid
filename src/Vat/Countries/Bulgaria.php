<?php

namespace Bespin\DataValidation\Vat\Countries;

class Bulgaria implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // BG 999999999 or BG 9999999999
        if (preg_match('/^(BG)(\d{9,10})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // BULGARIA  BG + 9 or 10 digits
    //
    // 9 digits → legal entities (UIC/BULSTAT)
    //   Primary weights  1..8: remainder < 10 → check digit
    //                          remainder = 10 → try alternative weights 3..10
    //                          alternative remainder < 10 → check digit
    //                          alternative remainder = 10 → check = 0
    //
    // 10 digits → physical persons (EGN, Единен граждански номер)
    //   Encodes birth date in d1–d6 (YYMMDD, month +20 for 1800s, +40 for 2000s)
    //   Checksum weights 2,4,8,5,10,9,7,3,6 on d1–d9; result mod 11 (10 → 0)
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^BG(\d{9,10})$/', $vatId, $m)) {
            return false;
        }
        return strlen($m[1]) === 9
            ? self::verifyLegalEntity($m[1])
            : self::verifyPhysicalPerson($m[1]);
    }

    private static function verifyLegalEntity(string $digits): bool
    {
        $d           = array_map('intval', str_split($digits));
        $weights     = [1, 2, 3, 4, 5, 6, 7, 8];
        $sum         = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $remainder = $sum % 11;

        if ($remainder < 10) {
            return $remainder === $d[8];
        }

        // Remainder is 10: retry with alternative weights
        $altWeights = [3, 4, 5, 6, 7, 8, 9, 10];
        $sum        = 0;
        for ($i = 0; $i < 8; $i++) {
            $sum += $d[$i] * $altWeights[$i];
        }
        $remainder = $sum % 11;
        $check     = $remainder < 10 ? $remainder : 0;

        return $check === $d[8];
    }

    private static function verifyPhysicalPerson(string $digits): bool
    {
        $d     = array_map('intval', str_split($digits));
        $month = (int)substr($digits, 2, 2);
        $day   = (int)substr($digits, 4, 2);

        // Decode century offset from month
        if ($month > 40) {
            $month -= 40; // born 2000–2099
        } elseif ($month > 20) {
            $month -= 20; // born 1800–1899
        }

        if ($month < 1 || $month > 12 || $day < 1 || $day > 31) {
            return false;
        }

        // Checksum
        $weights = [2, 4, 8, 5, 10, 9, 7, 3, 6];
        $sum     = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = $sum % 11;
        $check = $check >= 10 ? 0 : $check;

        return $check === $d[9];
    }
}