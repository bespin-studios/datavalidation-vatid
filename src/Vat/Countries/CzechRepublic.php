<?php

namespace Bespin\DataValidation\Vat\Countries;

class CzechRepublic implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // CZ 99999999 (8–10 digits)
        if (preg_match('/^(CZ)(\d{8,10})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // CZECH REPUBLIC  CZ + 8, 9, or 10 digits
    // 8 digits  → legal entity; weighted checksum mod 11
    // 9 digits  → individual born before 1954 (birth number, no check digit)
    // 10 digits → individual born 1954+; full birth number divisible by 11
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^CZ(\d{8,10})$/', $vatId, $m)) {
            return false;
        }
        $digits = $m[1];

        return match (strlen($digits)) {
            8       => self::verifyLegalEntity($digits),
            9       => self::verifyBirthNumber9($digits),
            10      => self::verifyBirthNumber10($digits),
            default => false,
        };
    }

    // weights 8..2 on d1..d7; check = (11 - sum%11) % 10
    // remainder of 1 means no valid check digit exists → invalid
    private static function verifyLegalEntity(string $digits): bool
    {
        $d       = array_map('intval', str_split($digits));
        $weights = [8, 7, 6, 5, 4, 3, 2];
        $sum     = 0;
        for ($i = 0; $i < 7; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $check = (11 - ($sum % 11)) % 11 % 10;
        return $check === $d[7];
    }

    // 9-digit birth numbers: no check digit; validate encoded month only
    private static function verifyBirthNumber9(string $digits): bool
    {
        $month = (int)substr($digits, 2, 2);
        $month = $month > 50 ? $month - 50 : $month; // +50 offset for women
        return $month >= 1 && $month <= 12;
    }

    // 10-digit birth numbers: the whole number must be divisible by 11
    private static function verifyBirthNumber10(string $digits): bool
    {
        if ((int)$digits % 11 !== 0) {
            return false;
        }
        $month = (int)substr($digits, 2, 2);
        $month = $month > 50 ? $month - 50 : $month;
        return $month >= 1 && $month <= 12;
    }
}