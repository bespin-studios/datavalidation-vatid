<?php

namespace Bespin\DataValidation\Vat\Countries;

class EuropeanUnion implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // EU 999 999 999
        if (preg_match('/^(EU)(\d{3})(\d{3})(\d{3})$/', $vatId, $m)) {
            return $m[1].' '.$m[2].' '.$m[3].' '.$m[4];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // EU VAT NUMBER  EU + 9 digits
    // Issued by the European Commission to non-EU entities (e.g. OSS/IOSS).
    // No checksum algorithm is publicly defined; validation is structural only.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        return (bool)preg_match('/^EU\d{9}$/', $vatId);
    }
}