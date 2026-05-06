<?php

namespace Bespin\DataValidation\Vat\Countries;

interface CountryInterface
{
    public static function format(string $vatId): string;

    public static function verify(string $vatId): bool;
}