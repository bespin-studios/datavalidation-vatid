<?php

namespace Bespin\DataValidation\Vat\Countries;

class Ireland implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // New format: IE 1234567 A  or  IE 1234567 AW
        if (preg_match('/^(IE)(\d{7})([A-Z]{1,2})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        // Old format: IE 1A23456 B
        if (preg_match('/^(IE)(\d[A-Z]\d{5})([A-Z])$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // IRELAND  IE + 8 or 9 characters
    //
    // New format:  7 digits + 1 check letter
    // Newer format: 7 digits + 2 letters (second letter is always W, for certain
    //               entity types; only the first letter is the check character)
    // Old format:  digit + letter + 5 digits + check letter
    //              (the letter encodes a value A=1..Z=26 in the weighted sum)
    //
    // Checksum: weights 8,7,6,5,4,3,2 on 7 input values;
    //           check = 'WABCDEFGHIJKLMNOPQRSTUV'[sum % 23]
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        // New format: 7 digits + 1 check letter
        if (preg_match('/^IE(\d{7})([A-W])$/', $vatId, $m)) {
            return self::verifyNewFormat($m[1], $m[2]);
        }
        // Newer format: 7 digits + letter + W  (W suffix for certain entity types)
        if (preg_match('/^IE(\d{7})([A-W])W$/', $vatId, $m)) {
            return self::verifyNewFormat($m[1], $m[2]);
        }
        // Old format: digit + letter + 5 digits + check letter
        if (preg_match('/^IE(\d)([A-Z])(\d{5})([A-W])$/', $vatId, $m)) {
            return self::verifyOldFormat($m[1], $m[2], $m[3], $m[4]);
        }
        return false;
    }

    private static function verifyNewFormat(string $sevenDigits, string $checkChar): bool
    {
        $d       = array_map('intval', str_split($sevenDigits));
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        return $checkChar === 'WABCDEFGHIJKLMNOPQRSTUV'[$sum % 23];
    }

    private static function verifyOldFormat(
        string $firstDigit,
        string $letter,
        string $fiveDigits,
        string $checkChar
    ): bool {
        // Letter encodes a numeric value: A=1, B=2, ..., Z=26
        $letterVal = ord($letter) - ord('A') + 1;

        $positions = array_merge(
            [(int)$firstDigit, $letterVal],
            array_map('intval', str_split($fiveDigits))
        );
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += $positions[$i] * $weights[$i];
        }
        return $checkChar === 'WABCDEFGHIJKLMNOPQRSTUV'[$sum % 23];
    }
}