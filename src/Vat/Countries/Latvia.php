<?php

namespace Bespin\DataValidation\Vat\Countries;

class Latvia implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // LV 99999999999
        if (preg_match('/^(LV)(\d{11})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // LATVIA  LV + 11 digits
    //
    // Two sub-types identified by first digit:
    //
    // Legal entities (d1 ∈ 4–9):
    //   Weights 9,1,4,8,3,10,2,5,7,6 on d1–d10;
    //   r = sum % 11
    //   r < 4  → check = 3 - r
    //   r == 4 → invalid (no valid check digit exists)
    //   r > 4  → check = 14 - r
    //   Compare with d11.
    //
    // Physical persons (d1 ∈ 0–3):
    //   Personal ID number encoding date of birth; no numeric checksum
    //   is publicly defined — structural validation only.
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^LV(\d{11})$/', $vatId, $m)) {
            return false;
        }
        $digits = $m[1];
        $first  = (int)$digits[0];

        return $first >= 4
            ? self::verifyLegalEntity($digits)
            : self::verifyPhysicalPerson($digits);
    }

    private static function verifyLegalEntity(string $digits): bool
    {
        $d       = array_map('intval', str_split($digits));
        $weights = [9, 1, 4, 8, 3, 10, 2, 5, 7, 6];
        $sum     = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $d[$i] * $weights[$i];
        }
        $r = $sum % 11;
        if ($r === 4) {
            return false;
        }
        $check = $r < 4 ? 3 - $r : 14 - $r;
        return $check === $d[10];
    }

    private static function verifyPhysicalPerson(string $digits): bool
    {
        // Format: DDMMYY CNNNN where C is century digit (0=1800s, 1=1900s, 2=2000s)
        $day     = (int)substr($digits, 0, 2);
        $month   = (int)substr($digits, 2, 2);
        $century = (int)$digits[6];

        return $day >= 1 && $day <= 31
            && $month >= 1 && $month <= 12
            && $century <= 2;
    }
}