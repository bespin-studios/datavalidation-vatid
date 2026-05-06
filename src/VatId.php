<?php

namespace Bespin\DataValidation;

use Bespin\DataValidation\Shared\FormatterInterface;
use Bespin\DataValidation\Vat\Countries\CountryInterface;
use Bespin\DataValidation\Vat\Countries\EuropeanUnion;
use Exception;
use Throwable;

class VatId implements FormatterInterface
{
    public static function verify(string $vatId, ?Country $country = null): bool
    {
        try {
            $normalised = self::convertToMachineReadable($vatId);
        } catch (Throwable) {
            return false;
        }
        $countryObject = self::resolveCountryObject($country, $vatId);
        return $countryObject::verify($normalised);
    }

    public static function convertToHumanReadable(string $input, Country $country, bool $isMachineReadable = false): string
    {
        if (!$isMachineReadable) {
            $input = self::convertToMachineReadable($input);
        }
        $countryObject = self::resolveCountryObject($country, $input);
        return $countryObject::format($input);
    }

    public static function convertToMachineReadable(string $input, bool $isMachineReadable = false): string
    {
        if ($isMachineReadable) {
            return $input;
        }
        $input = preg_replace('/[^A-Z0-9]/', '', strtoupper($input)) ?? '';
        if (empty($input)) {
            throw new Exception('failed to format VAT ID');
        }
        return $input;
    }

    private static function getCountryObject(Country $country): ?CountryInterface
    {
        $className = '\\Bespin\\DataValidation\\Vat\\Countries\\'.$country->name;
        if (class_exists($className)) {
            return new $className();
        }
        return null;
    }

    private static function resolveCountryObject(?Country $country, string $vatId): CountryInterface
    {
        // If country is known, use it directly
        if ($country !== null) {
            $obj = self::getCountryObject($country);
            if ($obj === null) {
                throw new Exception('country '.$country->name.' not supported');
            }
            return $obj;
        }

        // Try to identify from VAT ID prefix
        $code    = strtoupper(substr($vatId, 0, 2));
        $country = Country::byCode($code);

        if ($country !== null) {
            $obj = self::getCountryObject($country);
            if ($obj === null) {
                throw new Exception('country '.$country->name.' not supported');
            }
            return $obj;
        }

        // Non-country prefixes
        return match ($code) {
            'EU'    => new EuropeanUnion(),
            default => throw new Exception('country could not be identified from prefix: '.$code),
        };
    }
}