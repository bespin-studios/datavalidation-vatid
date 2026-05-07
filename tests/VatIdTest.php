<?php

namespace Bespin\DataValidation\Tests;

use Bespin\DataValidation\Country;
use Bespin\DataValidation\VatId;
use PHPUnit\Framework\TestCase;

class VatIdTest extends TestCase
{
    // --- VERIFY ---

    // Austria
    public function testVerifyAustriaValid(): void
    {
        $this->assertTrue(VatId::verify('ATU13585627', Country::Austria));
    }

    public function testVerifyAustriaInvalid(): void
    {
        $this->assertFalse(VatId::verify('ATU13585628', Country::Austria));
    }

    // Belgium
    public function testVerifyBelgiumValid(): void
    {
        $this->assertTrue(VatId::verify('BE0776091951', Country::Belgium));
    }

    public function testVerifyBelgiumInvalid(): void
    {
        $this->assertFalse(VatId::verify('BE0776091952', Country::Belgium));
    }

    // Croatia
    public function testVerifyCroatiaValid(): void
    {
        $this->assertTrue(VatId::verify('HR33392005961', Country::Croatia));
    }

    public function testVerifyCroatiaInvalid(): void
    {
        $this->assertFalse(VatId::verify('HR33392005962', Country::Croatia));
    }

    // Denmark
    public function testVerifyDenmarkValid(): void
    {
        $this->assertTrue(VatId::verify('DK13585628', Country::Denmark));
    }

    public function testVerifyDenmarkInvalid(): void
    {
        $this->assertFalse(VatId::verify('DK13585629', Country::Denmark));
    }

    // Finland
    public function testVerifyFinlandValid(): void
    {
        $this->assertTrue(VatId::verify('FI13669598', Country::Finland));
    }

    public function testVerifyFinlandInvalid(): void
    {
        $this->assertFalse(VatId::verify('FI13669599', Country::Finland));
    }

    // France
    public function testVerifyFranceNumericKeyValid(): void
    {
        $this->assertTrue(VatId::verify('FR83404833048', Country::France));
    }

    public function testVerifyFranceNumericKeyInvalid(): void
    {
        $this->assertFalse(VatId::verify('FR84404833048', Country::France));
    }

    public function testVerifyFranceAlphanumericKeyValid(): void
    {
        $this->assertTrue(VatId::verify('FRK7300070025', Country::France));
    }

    // Germany
    public function testVerifyGermanyValid(): void
    {
        $this->assertTrue(VatId::verify('DE345789001', Country::Germany));
        $this->assertTrue(VatId::verify('DE813113094,', Country::Germany));
    }

    public function testVerifyGermanyInvalid(): void
    {
        $this->assertFalse(VatId::verify('DE345789004', Country::Germany));
    }

    public function testVerifyGermanyLeadingZeroInvalid(): void
    {
        $this->assertFalse(VatId::verify('DE045789003', Country::Germany));
    }

    // Italy
    public function testVerifyItalyValid(): void
    {
        $this->assertTrue(VatId::verify('IT00743110157', Country::Italy));
    }

    public function testVerifyItalyInvalid(): void
    {
        $this->assertFalse(VatId::verify('IT00743110158', Country::Italy));
    }

    // Luxembourg
    public function testVerifyLuxembourgValid(): void
    {
        $this->assertTrue(VatId::verify('LU21416127', Country::Luxembourg));
    }

    public function testVerifyLuxembourgInvalid(): void
    {
        $this->assertFalse(VatId::verify('LU21416128', Country::Luxembourg));
    }

    // Netherlands
    public function testVerifyNetherlandsValid(): void
    {
        $this->assertTrue(VatId::verify('NL123456782B01', Country::Netherlands));
    }

    public function testVerifyNetherlandsInvalid(): void
    {
        $this->assertFalse(VatId::verify('NL123456789B01', Country::Netherlands));
    }

    // Poland
    public function testVerifyPolandValid(): void
    {
        $this->assertTrue(VatId::verify('PL8567346215', Country::Poland));
    }

    public function testVerifyPolandInvalid(): void
    {
        $this->assertFalse(VatId::verify('PL8567346216', Country::Poland));
    }

    // Portugal
    public function testVerifyPortugalValid(): void
    {
        $this->assertTrue(VatId::verify('PT545259045', Country::Portugal));
    }

    public function testVerifyPortugalInvalid(): void
    {
        $this->assertFalse(VatId::verify('PT545259046', Country::Portugal));
    }

    // Slovenia
    public function testVerifySloveriaValid(): void
    {
        $this->assertTrue(VatId::verify('SI15012557', Country::Slovenia));
    }

    public function testVerifySloveriaInvalid(): void
    {
        $this->assertFalse(VatId::verify('SI15012558', Country::Slovenia));
    }

    // Spain
    public function testVerifySpainJuridicalValid(): void
    {
        $this->assertTrue(VatId::verify('ESA13585625', Country::Spain));
    }

    public function testVerifySpainJuridicalInvalid(): void
    {
        $this->assertFalse(VatId::verify('ESA13585626', Country::Spain));
    }

    public function testVerifySpainPersonalValid(): void
    {
        // 99999999 mod 23 = 1 → table[1] = 'R' ✓
        $this->assertTrue(VatId::verify('ES12345678Z', Country::Spain));
    }

    public function testVerifySpainPersonalInvalid(): void
    {
        $this->assertFalse(VatId::verify('ES99999999S', Country::Spain));
    }

    // Sweden
    public function testVerifySwedenValid(): void
    {
        // 5560000001 with weights 2,1,2,1… → sum = 10, mod 10 = 0 ✓
        $this->assertTrue(VatId::verify('SE556000000101', Country::Sweden));
    }

    public function testVerifySwedenInvalid(): void
    {
        $this->assertFalse(VatId::verify('SE556000000201', Country::Sweden));
    }

    public function testVerifySwedenWrongSuffixInvalid(): void
    {
        $this->assertFalse(VatId::verify('SE556000000100', Country::Sweden));
    }

    // --- FORMAT (machine) ---

    public function testFormatStripsSpaces(): void
    {
        $this->assertSame('DE345789003', VatId::convertToMachineReadable('DE 345 789 003'));
    }

    public function testFormatStripsDashes(): void
    {
        $this->assertSame('PL8567346215', VatId::convertToMachineReadable('PL856-734-62-15'));
    }

    public function testFormatUppercases(): void
    {
        $this->assertSame('DE345789003', VatId::convertToMachineReadable('de345789003'));
    }

    // --- FORMAT (human) ---

    public function testFormatHumanGermany(): void
    {
        $this->assertSame('DE 345 789 003', VatId::convertToHumanReadable('DE345789003', Country::Germany));
    }

    public function testFormatHumanAustria(): void
    {
        $this->assertSame('ATU 13585627', VatId::convertToHumanReadable('ATU13585627', Country::Austria));
    }

    public function testFormatHumanNetherlands(): void
    {
        $this->assertSame('NL 123456782 B01', VatId::convertToHumanReadable('NL123456782B01', Country::Netherlands));
    }

    public function testFormatHumanPoland(): void
    {
        $this->assertSame('PL 856-734-62-15', VatId::convertToHumanReadable('PL8567346215', Country::Poland));
    }

    public function testFormatHumanFrance(): void
    {
        $this->assertSame('FR 83 404 833 048', VatId::convertToHumanReadable('FR83404833048', Country::France));
    }
}