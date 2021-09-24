# Currency Rate Downloader

![Laravel Version Support](https://img.shields.io/badge/Laravel-8.x-orange?style=flat-square)
![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/flexmind-software/currency-rate?label=PHP&style=flat-square)
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
    'driver' => env('FLEXMIND_CURRENCY_RATE_DRIVER', 'poland'),
    'table-name' => env('FLEXMIND_CURRENCY_RATE_TABLENAME', 'currency_rates')
];
```

## Usage

```bash
php artisan flexmind:currency-rate [options] [--] [<date>]

Arguments:
  date               Date to download currency rate, if empty is today
  
Options:
  --queue[=QUEUE]    Queue name, if set "none" cmd run without add job to queue [default: "none"]
  --driver[=DRIVER]  Driver to download rate [default: "all"]
```

## Testing

```bash
composer test
```

## Sources

<table>
    <thead>
    <tr>
        <th>Country name</th>
        <th>Central Bank</th>
        <th>Driver name</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4">Europe</td>
        </tr>
        <tr>
            <td></td>
            <td><a href="https://ecb.europa.eu" target="_blank">European Central Bank</a></td>
            <td>european-central-bank</td>
        </tr>
        <tr>
            <td>Albania</td>
            <td><a href="https://www.bankofalbania.org/home" target="_blank">Bankës së Shqipërisë</a></td>
            <td>albania</td>
        </tr>          
        <tr>
            <td>Armenia</td>
            <td><a href="https://www.cba.am/en/sitepages/default.aspx" target="_blank">Hayastani Hanrapetut’yan Kentronakan Bank</a></td>
            <td>armenia</td>
        </tr>        
        <tr>
            <td>Azerbaijan</td>
            <td><a href="https://www.cbar.az" target="_blank">Azərbaycan Mərkəzi Bankı</a></td>
            <td>azerbaijan</td>
        </tr>
        <tr>
            <td>Belarus</td>
            <td><a href="https://www.nbrb.by/engl/" target="_blank">Нацыянальны банк Рэспублікі Беларусь</a></td>
            <td>belarus</td>
        </tr>
        <tr>
            <td>Bosnia and Herzegovina</td>
            <td><a href="https://www.cbbh.ba/?lang=en" target="_blank">Централна банка Босне и Херцеговине</a></td>
            <td>bosnia-and-herzegovina</td>
        </tr>
        <tr>
            <td>Bulgaria</td>
            <td><a href="https://www.bnb.bg/?toLang=_EN" target="_blank">Bŭlgarska narodna banka</a></td>
            <td>bulgaria</td>
        </tr>
        <tr>
            <td>Croatia</td>
            <td><a href="https://www.hnb.hr/home" target="_blank">Hrvatska Narodna Banka</a></td>
            <td>croatia</td>
        </tr>
        <tr>
            <td>Czech Republic</td>
            <td><a href="https://www.cnb.cz/en/index.html" target="_blank">Česká národní banka</a></td>
            <td>czech-republic</td>
        </tr>
        <tr>
            <td>Danmark</td>
            <td><a href="https://www.nationalbanken.dk" target="_blank">Danmarks Nationalbanks</a></td>
            <td>denmark</td>
        </tr>
        <tr>
            <td>Georgia</td>
            <td><a href="https://www.nbg.gov.ge" target="_blank">Sakartvelos Erovnuli Bank’i</a></td>
            <td>georgia</td>
        </tr>
        <tr>
            <td>United Kingdom</td>
            <td><a href="https://www.bankofengland.co.uk/" target="_blank">Bank of England</a></td>
            <td>england</td>
        </tr>
        <tr>
            <td>Hungary</td>
            <td><a href="https://www.mnb.hu/en/" target="_blank">Magyar Nemzeti Bank</a></td>
            <td>hungary</td>
        </tr>
        <tr>
            <td>Iceland</td>
            <td><a href="https://cb.is" target="_blank">Seðlabanki Íslands</a></td>
            <td>iceland</td>
        </tr>
        <tr>
            <td>Macedonia</td>
            <td><a href="https://www.nbrm.mk/" target="_blank">Narodna Banka na Republika Severna Makedonija</a></td>
            <td>macedonia</td>
        </tr>          
        <tr>
            <td>Moldavia</td>
            <td><a href="https://www.bnm.md/" target="_blank">Banca Naţională a Moldovei</a></td>
            <td>moldavia</td>
        </tr>        
        <tr>
            <td>Norway</td>
            <td><a href="https://www.norges-bank.no/en/" target="_blank">Norges Bank</a></td>
            <td>norway</td>
        </tr>
        <tr>
            <td>Poland</td>
            <td><a href="https://www.nbp.pl/" target="_blank">Narodowy Bank Polski</a></td>
            <td>poland</td>
        </tr>
        <tr>
            <td>Russia</td>
            <td><a href="https://cbr.ru/" target="_blank">Tsentral'nyy bank Rossiyskoy Federatsii</a></td>
            <td>russia</td>
        </tr>
        <tr>
            <td>Romania</td>
            <td><a href="https://www.bnro.ro/" target="_blank">Banca Națională a României</a></td>
            <td>romania</td>
        </tr>        
        <tr>
            <td>Serbia</td>
            <td><a href="https://www.nbs.rs/" target="_blank">Narodna banka Srbije</a></td>
            <td>serbia</td>
        </tr>
        <tr>
            <td>Switzerland</td>
            <td><a href="https://www.snb.ch/" target="_blank">Banca naziunala svizra</a></td>
            <td>switzerland</td>
        </tr>
        <tr>
            <td>Sweden</td>
            <td><a href="https://www.riksbank.se/en-gb/" target="_blank">Sveriges Riksbank</a></td>
            <td>sweden</td>
        </tr>
        <tr>
            <td>Turkey</td>
            <td><a href="https://www.tcmb.gov.tr/" target="_blank">Türkiye Cumhuriyet Merkez Bankası</a></td>
            <td>turkey</td>
        </tr>
        <tr>
            <td>Ukraine</td>
            <td><a href="https://www.bank.gov.ua/" target="_blank">Natsionalʹnyy bank Ukrayiny</a></td>
            <td>ukraine</td>
        </tr>
        <tr>
            <td colspan="4">Africa</td>
        </tr>
        <tr>
            <td>Benin</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td>Botswana</td>
            <td><a href="https://www.bankofbotswana.bw" target="_blank">Bank of Botswana</a></td>
            <td>botswana</td>
        </tr>
        <tr>
            <td>Burkina Faso</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td>Guinea-Bissau</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td>Ivory Coast</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td>Mali</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td>Niger</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td>Senegal</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td>Togo</td>
            <td><a href="https://www.bceao.int" target="_blank">Central Bank of West African States (BCEAO)</a></td>
            <td>bceao</td>
        </tr>
        <tr>
            <td colspan="4">Asia</td>
        </tr>
        <tr>
            <td>Israel</td>
            <td><a href="https://www.boi.org.il/" target="_blank">בנק ישראל</a></td>
            <td>israel</td>
        </tr>
        <tr>
            <td colspan="4">North America</td>
        </tr>
        <tr>
            <td>Canada</td>
            <td><a href="https://www.bankofcanada.ca/" target="_blank">Banqueu du Canada</a></td>
            <td>canada</td>
        </tr>
        <tr>
            <td colspan="4">Oceania</td>
        </tr>
        <tr>
            <td>Australia</td>
            <td><a href="https://www.rba.gov.au/" target="_blank">Reserve Bank of Australia</a></td>
            <td>australia</td>
        </tr>
    </tbody>
</table>

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Krzysztof Bielecki](https://github.com/qwerkon)
- [All Contributors](https://github.com/flexmind-software/currency-rate/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
