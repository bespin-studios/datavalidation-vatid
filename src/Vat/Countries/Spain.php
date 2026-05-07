<?php

namespace Bespin\DataValidation\Vat\Countries;

class Spain implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // ES X9999999X
        if (preg_match('/^(ES)([A-Z0-9]\d{7}[A-Z0-9])$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // SPAIN  ES + (letter)(7 digits)(letter|digit) for most entity types
    // Several sub-types; checksum letter for juridical entities (NIF/CIF).
    // ---------------
    public static function verify(string $vatId): bool
    {
        if (str_starts_with($vatId, 'ES')) {
            $vatId = substr($vatId, 2);
        }

        // Juridical: letter + 1-7 digits + check digit
        if (preg_match('/^([ABCDEFGHJNPQRSUVW])(\d{1,7})(\d)$/', $vatId, $s)) {
            $digits = str_pad($s[2], 7, '0', STR_PAD_LEFT);
            return self::verifySpainJuridical($digits, (int)$s[3]);
        }

        // Juridical: letter + 1-7 digits + check letter (types N,P,Q,R,S,W require letter check)
        if (preg_match('/^([ABCDEFGHJNPQRSUVW])(\d{1,7})([A-Z])$/', $vatId, $s)) {
            $digits = str_pad($s[2], 7, '0', STR_PAD_LEFT);
            return self::verifySpainJuridicalLetter($digits, $s[3]);
        }

        // Personal NIF: 8 digits + letter (plain DNI)
        if (preg_match('/^(\d{8})([A-Z])$/', $vatId, $s)) {
            $table = 'TRWAGMYFPDXBNJZSQVHLCKE';
            return $s[2] === $table[(int)$s[1] % 23];
        }

        // Personal NIE: Y/Z prefix + 7 digits + letter
        if (preg_match('/^([YZ])(\d{7})([A-Z])$/', $vatId, $s)) {
            $table  = 'TRWAGMYFPDXBNJZSQVHLCKE';
            $prefix = $s[1] === 'Y' ? 1 : 2;
            $number = (int)(''.$prefix.$s[2]);
            return $s[3] === $table[$number % 23];
        }

        // Special legal entities: K, L, M, X prefix + 7 digits + letter
        if (preg_match('/^([KLMX])(\d{7})([A-Z])$/', $vatId, $s)) {
            $table  = 'TRWAGMYFPDXBNJZSQVHLCKE';
            $number = (int)$s[2];
            return $s[3] === $table[$number % 23];
        }

        return false;
    }

    private static function verifySpainJuridical(string $digits, int $checkDigit): bool
    {
        $sum = 0;
        for ($i = 0; $i < 7; $i++) {
            $c   = (int)$digits[$i] * ($i % 2 === 0 ? 2 : 1);
            $sum += $c > 9 ? $c - 9 : $c;
        }
        $calculated = (10 - ($sum % 10)) % 10;
        return $checkDigit === $calculated;
    }

    private static function verifySpainJuridicalLetter(string $digits, string $checkLetter): bool
    {
        $letters = 'JABCDEFGHI';
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $c   = (int)$digits[$i] * ($i % 2 === 0 ? 2 : 1);
            $sum += $c > 9 ? $c - 9 : $c;
        }
        // Use sum % 10 directly as index (NOT the (10 - sum%10) % 10 inversion)
        return $checkLetter === $letters[$sum % 10];
    }
}