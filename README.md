# Currency Rate Downloader

[![Latest Version on Packagist](https://img.shields.io/packagist/v/flexmind-software/currency-rate.svg?style=flat-square)](https://packagist.org/packages/flexmind-software/currency-rate)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/flexmind-software/currency-rate/run-tests?label=tests)](https://github.com/flexmind-software/currency-rate/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/flexmind-software/currency-rate/Check%20&%20fix%20styling?label=code%20style)](https://github.com/flexmind-software/currency-rate/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/flexmind-software/currency-rate.svg?style=flat-square)](https://packagist.org/packages/flexmind-software/currency-rate)

## Installation

You can install the package via composer:

```bash
composer require flexmind-software/currency-rate
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="FlexMindSoftware\CurrencyRate\CurrencyRateProvider" --tag="currency-rate-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="FlexMindSoftware\CurrencyRate\CurrencyRateProvider" --tag="currency-rate-config"
```

This is the contents of the published config file:

```php
return [
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'bank-of-poland'),
    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates'),
    'supported-currency' => env('FLEXMIND_CURRENCY_RATE_SUPPORTED_CURRENCY', [])
];
```

## Usage

```bash
php artisan flexmind:currency-rate [options] [--] [<date>]

Arguments:
  date               Date to download currency rate, if empty is today
  
Options:
  --driver[=DRIVER]  Driver to download rate [default: "default"]
```
## Testing

```bash
composer test
```

## Sources

### Europe
- [x] [European Central Bank](https://ecb.europa.eu) (driver name 'european-central-bank')
- [ ] [Bank of Albania / Banka e Shqiperise](https://www.bankofalbania.org/home/)
- [ ] [Bank of Armenia / Hayastani Hanrapetut’yan Kentronakan Bank](https://www.cba.am/en/sitepages/default.aspx)
- [ ] [Bank of Belarus / Natsional'nyy bank Respubliki Belarus'](http://www.nbrb.by/engl/)
- [ ] [Bank of Bosnia and Herzegovina / Centralna Banka Bosne I Hergegovine](https://www.cbbh.ba/?lang=en)
- [x] [Bank of Bulgaria / Bŭlgarska narodna banka](http://www.bnb.bg/?toLang=_EN) (driver name 'bank-of-bulgaria')
- [ ] [Bank of Croatia / Hrvatska Narodna Banka](https://www.hnb.hr/home)
- [x] [Bank of Czech Republic / Ceska Narodni Banka](https://www.cnb.cz/en/index.html) (driver name 'bank-of-czech-republic')
- [x] [Bank of Danmark / Danmarks Nationalbanks](http://www.nationalbanken.dk/en) (driver name 'bank-of-denmark')
- [ ] [Bank of England](https://www.bankofengland.co.uk/)
- [ ] [Bank of Hungary / Magyar Nemzeti Bank](https://www.mnb.hu/en/)
- [ ] [Bank of Ireland / Banc Ceannais na heireann](https://www.centralbank.ie/)
- [ ] [Bank of Norway / Norges Bank](https://www.norges-bank.no/en/)
- [x] [Bank of Poland / Narodowy Bank Polski](https://www.nbp.pl/) (driver name 'bank-of-poland')
- [ ] [Bank of Romania / Banca Nationala a Romaniei](https://www.bnro.ro/Home.aspx)
- [ ] [Bank of Serbia / Narodna banka Srbije](https://www.nbs.rs/en/indeks/index.html)
- [ ] [Bank of Slovenia / Banka Slovenije](https://www.bsi.si/en/) 
- [ ] [Bank of Sweden / Sveriges Riksbank](https://www.riksbank.se/en-gb/)

### Asia


### Africa


### North America
- [x] [Bank of Canada / Banqueu du Canada](https://www.bankofcanada.ca/) (driver name 'bank-of-canada')

### South America
- [ ] [Bank of Argentina / Banco Central de la Republica Argentina](http://www.bcra.gob.ar/default.asp)
- [ ] [Bank of Brasil / Banco Central Do Brasil](https://www.bcb.gov.br/en)
- [ ] [Bank of Chile / Banco Central de Chile](https://www.bcentral.cl/en/web/banco-central)

### Oceania
- [ ] [Bank of Australia / Reserve Bank of Australia](https://www.rba.gov.au/)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Krzysztof Bielecki](https://github.com/qwerkon)
- [All Contributors](http://github.com/flexmind-software/currency-rate/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
