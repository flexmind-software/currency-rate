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
  --queue[=QUEUE]    Queue name, if set "none" cmd run without add job to queue [default: "none"]
  --driver[=DRIVER]  Driver to download rate [default: "all"]
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
- [ ] [Bank of Azerbaijan / Azərbaycan Mərkəzi Bankı](https://www.cbar.az)
- [x] [Bank of Belarus / Natsional'nyy bank Respubliki Belarus'](http://www.nbrb.by/engl/)
- [x] [Bank of Bosnia and Herzegovina / Centralna Banka Bosne I Hergegovine](https://www.cbbh.ba/?lang=en)
- [x] [Bank of Bulgaria / Bŭlgarska narodna banka](http://www.bnb.bg/?toLang=_EN) (driver name 'bank-of-bulgaria')
- [ ] [Bank of Croatia / Hrvatska Narodna Banka](https://www.hnb.hr/home)
- [x] [Bank of Czech Republic / Ceska Narodni Banka](https://www.cnb.cz/en/index.html) (driver name 'bank-of-czech-republic')
- [x] [Bank of Danmark / Danmarks Nationalbanks](http://www.nationalbanken.dk/en) (driver name 'bank-of-denmark')
- [x] [Bank of Georgia / Sakartvelos Erovnuli Bank’i](http://www.nbg.gov.ge)
- [ ] [Bank of United Kingdom / Bank of England](https://www.bankofengland.co.uk/)
- [ ] [Bank of Hungary / Magyar Nemzeti Bank](https://www.mnb.hu/en/)
- [ ] [Bank of Iceland / Seðlabanki Íslands)](https://cb.is)
- [ ] [Bank of Liechtenstein / Liechtensteinische Landesbank](https://www.llb.li/en)
- [ ] [Bank of Macedonia / Narodna Banka na Republika Severna Makedonija](http://www.nbrm.mk/)
- [ ] [Bank of Moldavia / Banca Naţională a Moldovei](http://www.bnm.md/)
- [ ] [Bank of Norway / Norges Bank](https://www.norges-bank.no/en/)
- [x] [Bank of Poland / Narodowy Bank Polski](https://www.nbp.pl/) (driver name 'bank-of-poland')
- [ ] [Bank of Russia / Tsentral'nyy bank Rossiyskoy Federatsii](http://cbr.ru/)
- [ ] [Bank of Romania / Banca Nationala a Romaniei](https://www.bnro.ro/Home.aspx)
- [ ] [Bank of Serbia / Narodna banka Srbije](https://www.nbs.rs/en/indeks/index.html)
- [ ] [Bank of Switzerland / Banca naziunala svizra](http://www.snb.ch/)
- [ ] [Bank of Sweden / Sveriges Riksbank](https://www.riksbank.se/en-gb/)
- [ ] [Bank of Turkey / Türkiye Cumhuriyet Merkez Bankası](http://www.tcmb.gov.tr/)
- [ ] [Bank of Ukraine / Natsionalʹnyy bank Ukrayiny](http://www.bank.gov.ua/)

### Asia
- [ ] [Bank of China / Zhōngguó Rénmín Yínháng](http://www.pbc.gov.cn/en/3688006/index.html)
- [ ] [Bank of India / ](http://rbi.org.in/)
- [ ] [Bank of Indonesia / ](http://www.bi.go.id/)
- [ ] [Bank of Iran / Bank Markazi-ye Jomhuri-ye Eslāmi-ye Irān](http://www.cbi.ir/default_en.aspx)
- [ ] [Bank of Iraq / Albank Almarkaziu Aleiraqiu](https://www.cbi.iq/)
- [ ] [Bank of Japan / Nippon Ginkō](http://www.boj.or.jp/en/)
- [ ] [Bank of Israel / בנק ישראל](https://www.boi.org.il/)
- [ ] [Bank of Korea / Hanguk Eunhaeng](http://www.bok.or.kr/eng/)
- [ ] [Bank of Lebanon / مصرف لبنان](http://www.bdl.gov.lb/)
- [ ] [Bank of Myanmar / မြန်မာနိုင်ငံတော်ဗဟိုဘဏ်](http://www.cbm.gov.mm/)
- [ ] [Bank of Nepal / Nepal Rastra Bank](https://www.nrb.org.np/)
- [ ] [Bank of Pakistan / بینک دَولتِ پاکِستان](http://www.sbp.org.pk/)
- [ ] [Bank of Philippines / Bangko Sentral ng Pilipinas](http://www.bsp.gov.ph/)
- [ ] [Bank of Sri Lanka / ශ්‍රී ලංකා මහ බැංකුව](http://www.cbsl.gov.lk/)
- [ ] [Bank of Syria / مصرف سورية المركزي](http://cb.gov.sy/en)
- [ ] [Bank of the Republic of China (Taiwan) / 中華民國中央銀行](https://www.cbc.gov.tw/en/mp-2.html)
- [ ] [Bank of Thailand / ธนาคารแห่งประเทศไทย](http://www.bot.or.th/)
- [ ] [Bank of Vietnam / Ngân hàng Nhà nước Việt Nam](http://www.sbv.gov.vn/)
- [ ] [Bank of Yemen / البنك الأهلي اليمني](www.nbyemen.com/iNav/index_ar.html)

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
