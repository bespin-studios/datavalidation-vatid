<?php

namespace Bespin\DataValidation\Vat\Countries;

class France implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // FR XX 999 999 999
        if (preg_match('/^(FR)([0-9A-Z]{2})(\d{3})(\d{3})(\d{3})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3].' '.$m[4].' '.$m[5];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // FRANCE  FR + 2 key chars (digits or letters excl. I/O) + 9 digit SIREN
    // Numeric-only keys: key = (12 + 3 * (SIREN % 97)) % 97
    // Alphanumeric keys: structural check only (checksum not publicly documented)
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        // Allow O and I exclusions in the key positions per official spec
        if (!preg_match('/^FR([0-9A-HJ-NP-Z]{2})(\d{9})$/', $vatId, $m)) {
            return false;
        }
        $key   = $m[1];
        $siren = (int)$m[2];

        // Numeric key: validate checksum
        if (ctype_digit($key)) {
            $expected = (12 + 3 * ($siren % 97)) % 97;
            return (int)$key === $expected;
        }

        // Alphanumeric key: structural match is sufficient
        return true;
    }
}