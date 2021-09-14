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
];
```

## Usage



## Testing

```bash
composer test
```

## Sources

- [ ] [Bank of Canada / Banqueu du Canada](https://www.bankofcanada.ca/)
- [ ] [Danmark National Bank](http://www.nationalbanken.dk/en)
- [ ] [Bank of Estonia / Eesti Pank](https://eestipank.ee/en)
- [ ] [Bank of Sweden / Sveriges Riksbank](https://www.riksbank.se/en-gb/)
- [ ] [Bank of Norway / Norges Bank](https://www.norges-bank.no/en/)
- [ ] [European Central Bank](https://ecb.europa.eu)
- [ ] [Bank of Romania / Banca Nationala a Romaniei](https://www.bnro.ro/Home.aspx)
- [ ] [Bank of Bulgaria](http://www.bnb.bg/?toLang=_EN)
- [ ] [Bank of Hungary / Magyar Nemzeti Bank](https://www.mnb.hu/en/)
- [ ] [Bank of Netherlands / De Nederlandsche Bank](https://www.dnb.nl/en/)
- [ ] [Bank of England](https://www.bankofengland.co.uk/)
- [ ] [Bank of France / Banque de France](https://www.banque-france.fr/en)
- [ ] [Bank of Spain / Banco de Espana](https://www.bde.es/bde/en/)
- [ ] [Bank of Portugal / Banco de Portugal](https://www.bportugal.pt/en)
- [ ] [Bank of Italy / Banca d'Italia](https://www.bancaditalia.it/)
- [ ] [Bank of Germany / Deutsche Bundesbank](https://www.bundesbank.de/en/)
- [ ] [Bank of Slovenia / Banka Slovenije](https://www.bsi.si/en/)
- [x] [Bank of Czech Republic / Ceska Narodni Banka](https://www.cnb.cz/en/index.html)
- [ ] [Bank of Lithuania / Lietuvos Bankas](https://www.lb.lt/)
- [ ] [Bank of Latvia / Latvijas Banka](https://www.bank.lv/en/)
- [ ] [Bank of Slovakia / Narodna Banka Slovenska](https://www.nbs.sk/en/home)
- [x] [Bank of Poland / Narodowy Bank Polski](https://www.nbp.pl/)
- [ ] [Bank of Croatia / Hrvatska Narodna Banka](https://www.hnb.hr/home)
- [ ] [Bank of Serbia ](https://www.nbs.rs/internet/english)
- [ ] [Bank of Greece](https://www.bankofgreece.gr/en/homepage)
- [ ] [Bank of Ireland / Banc Ceannais na heireann](https://www.centralbank.ie/)
- [ ] [Bank of Malta / Bank Centrali ta'Malta](https://www.centralbankmalta.org/)
- [ ] [Bank of Cyprus](https://www.centralbank.cy/en/home)
- [ ] [Bank of Luxembourg / Banque Centrale du Luxembourg](http://www.bcl.lu/en/index.html)
- [ ] [Bank of Belgium / Banque Nationale de Belgique / Bank van Belgie](https://www.nbb.be/en)
- [ ] [Bank of Austria / Oesterreichische Nationalbank](https://www.oenb.at/en/)
- [ ] [Bank of Bosnia and Herzegovina / Centralna Banka Bosne I Hergegovine](https://www.cbbh.ba/?lang=en)
- [ ] [Bank of Australia](https://www.rba.gov.au/)
- [ ] [Bank of Albania / Banka e Shqiperise](https://www.bankofalbania.org/home/)
- [ ] [Bank of Armenia ](https://www.cba.am/en/sitepages/default.aspx)
- [ ] [Bank of Belarus](http://www.nbrb.by/engl/)
- [ ] [Bank of Argentina / Banco Central de la Republica Argentina](http://www.bcra.gob.ar/default.asp)
- [ ] [Central Bank of Brasil / Banco Central Do Brasil](https://www.bcb.gov.br/en)
- [ ] [Bank of Chile / Banco Central de Chile](https://www.bcentral.cl/en/web/banco-central)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Krzysztof Bielecki](https://github.com/qwerkon)
- [All Contributors](http://github.com/flexmind-software/currency-rate/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
