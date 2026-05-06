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
        if (!preg_match('/^ES([A-Z0-9]\d{7}[A-Z0-9])$/', $vatId, $m)) {
            return false;
        }
        $n = $m[1];

        // National juridical entities: letter + 8 digits
        if (preg_match('/^([ABCDEFGHJNPQRSUVW])(\d{7})(\d)$/', $n, $s)) {
            return self::verifySpainJuridical($s[2], (int)$s[3]);
        }

        // Other juridical entities: letter + 7 digits + letter check
        if (preg_match('/^([ABCDEFGHJNPQRSUVW])(\d{7})([A-J])$/', $n, $s)) {
            return self::verifySpainJuridicalLetter($s[2], $s[3]);
        }

        // Personal/physical entities (NIF): digit/Y/Z + 7 digits + letter
        if (preg_match('/^([0-9YZ])(\d{7})([A-Z])$/', $n, $s)) {
            return self::verifySpainPersonal($s[1], $s[2], $s[3]);
        }

        // Special personal (NIE): K/L/M/X + 7 digits + letter
        if (preg_match('/^([KLMX])(\d{7})([A-Z])$/', $n, $s)) {
            return self::verifySpainPersonal($s[1], $s[2], $s[3]);
        }

        return false;
    }
    private static function verifySpainJuridical(string $digits, int $checkDigit): bool
    {
        $table = [0, 9, 8, 7, 6, 5, 4, 3, 2, 1];
        $d     = array_map('intval', str_split($digits));
        $sum   = 0;
        for ($i = 0; $i < 7; $i++) {
            if ($i % 2 === 0) {
                $p   = $d[$i] * 2;
                $sum += $p > 9 ? $p - 9 : $p;
            } else {
                $sum += $d[$i];
            }
        }
        return $checkDigit === $table[$sum % 10];
    }

    private static function verifySpainJuridicalLetter(string $digits, string $checkLetter): bool
    {
        $letters = 'JABCDEFGHI';
        $d       = array_map('intval', str_split($digits));
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            if ($i % 2 === 0) {
                $p   = $d[$i] * 2;
                $sum += $p > 9 ? $p - 9 : $p;
            } else {
                $sum += $d[$i];
            }
        }
        return $checkLetter === $letters[$sum % 10];
    }

    private static function verifySpainPersonal(string $first, string $digits, string $checkLetter): bool
    {
        $table = 'TRWAGMYFPDXBNJZSQVHLCKE';
        // Y → 1, Z → 2, X → 0, K/L/M → treat leading as 0
        $prefix = match ($first) {
            'Y'     => '1',
            'Z'     => '2',
            default => '0',
        };
        $number = (int)($prefix.$digits);
        return $checkLetter === $table[$number % 23];
    }
}