# bespin/datavalidation-vatid
Library to verify vat ids

## List of supported countries
| Country | Name                              | Information                                                                                                      |
|---------|-----------------------------------|------------------------------------------------------------------------------------------------------------------|
| Germany | Umsatzsteuer-Identifikationsnummer | [Wikipedia - Umsatzsteuer-Identifikationsnummer](https://de.wikipedia.org/wiki/Umsatzsteuer-Identifikationsnummer) |
| France  | Numéro d'identification à la taxe sur la valeur ajoutée / Numéro de TVA intracommunautaire  | [Wikipedia - VAT identification number](https://en.wikipedia.org/wiki/VAT_identification_number) |

## How to use
```composer require bespin/datavalidation-vatid```
```
<?php
use Bespin\DataValidation;
if (DataValidation\VatId::verify('xxx', DataValidation\Country::Germany)) {
    echo 'xxx is a valid vatId';
} else {
    echo 'xxx is not a valid vatId';
}
```