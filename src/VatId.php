<?php

namespace Bespin\DataValidation;

use Bespin\DataValidation\Vat\Countries\CountryInterface;
use Exception;
use Throwable;

class VatId
{
    public static function verify(string $vatId, Country $country): bool
    {
        try {
            $normalised = self::format($vatId, $country);
        } catch (Throwable) {
            return false;
        }

        $countryObject = self::getCountryObject($country);
        if ($countryObject === null) {
            throw new Exception('country '.$country->name.' not supported');
        }
        return $countryObject::verify($normalised);
    }

    /**
     * Returns the machine-format VAT ID (uppercase, stripped of spaces/dashes/dots).
     * With Format::human and a country, returns the canonical human-readable representation.
     *
     * @throws Exception
     */
    public static function format(string $vatId, Country $country, Format $format = Format::machine, bool $isAlreadyMachineFormat = false): string
    {
        if ($format === Format::machine) {
            if ($isAlreadyMachineFormat) {
                return $vatId;
            }
            $vatId = strtoupper(preg_replace('/[\s\-\.]+/', '', $vatId) ?? '');
            if (empty($vatId)) {
                throw new Exception('failed to format VAT ID');
            }
            return $vatId;
        } else {
            if (!$isAlreadyMachineFormat) {
                $vatId = self::format($vatId, $country);
            }
            $countryObject = self::getCountryObject($country);
            if ($countryObject === null) {
                throw new Exception('country '.$country->name.' not supported');
            }
            return $countryObject::format($vatId);
        }
    }

    private static function getCountryObject(Country $country): ?CountryInterface
    {
        $className = '\\Bespin\\DataValidation\\Vat\\Countries\\'.$country->name;
        if (class_exists($className)) {
            return new $className();
        }
        return null;
    }
}