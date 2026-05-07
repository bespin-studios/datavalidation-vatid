# bespin/datavalidation-vatid

Library to verify vat ids

## List of supported countries

| Country          |
|------------------|
| Austria          |
| Belgium          |
| Bulgaria         |
| Croatia          |
| Cyprus           |
| CzechRepublic    |
| Denmark          |
| Estonia          |
| EuropeanUnion    |
| Finland          |
| France           |
| Germany          |
| Greece           |
| Hungary          |
| Ireland          |
| Italy            |
| Latvia           |
| Lithuania        |
| Luxembourg       |
| Malta            |
| Netherlands      |
| Poland           |
| Portugal         |
| Romania          |
| Slovakia         |
| Slovenia         |
| Spain            |
| Sweden           |
| UnitedKingdom    | 

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