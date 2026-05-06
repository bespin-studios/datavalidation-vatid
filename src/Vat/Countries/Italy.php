<?php

namespace Bespin\DataValidation\Vat\Countries;

class Italy implements CountryInterface
{

    public static function format(string $vatId): string
    {
        // IT 99999999999
        if (preg_match('/^(IT)(\d{11})$/', $vatId, $m)) {
            return $m[1].' '.$m[2];
        }
        return $vatId;
    }

    // -------------------------------------------------------------------------
    // ITALY  IT + 11 digits
    // Digits 1-7: progressive company number (must not be 0000000)
    // Digits 8-10: issuing office code (001-201, 999, 888)
    // Digit 11: Luhn-based check digit
    // -------------------------------------------------------------------------
    public static function verify(string $vatId): bool
    {
        if (!preg_match('/^IT(\d{11})$/', $vatId, $m)) {
            return false;
        }
        $d = array_map('intval', str_split($m[1]));

        // First 7 digits must not all be zero
        if (array_sum(array_slice($d, 0, 7)) === 0) {
            return false;
        }

        // Office code (digits 7–9, 0-indexed): 1–201, or 999, or 888
        $office = (int)($m[1][7].$m[1][8].$m[1][9]);
        if (!($office >= 1 && $office <= 201) && $office !== 999 && $office !== 888) {
            return false;
        }

        // Check digit: Luhn-style
        $sum = 0;
        for ($i = 0; $i <= 9; $i++) {
            if ($i % 2 === 0) {
                $sum += $d[$i];
            } else {
                $doubled = $d[$i] * 2;
                $sum     += $doubled > 9 ? $doubled - 9 : $doubled;
            }
        }
        $check = (10 - ($sum % 10)) % 10;
        return $check === $d[10];
    }
}