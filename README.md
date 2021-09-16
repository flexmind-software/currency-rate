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

### Europe
- [x] [European Central Bank](https://ecb.europa.eu) (driver name 'european-central-bank')
- [ ] [Albania / Banka e Shqiperise](https://www.bankofalbania.org/home/)
- [ ] [Armenia / Hayastani Hanrapetut’yan Kentronakan Bank](https://www.cba.am/en/sitepages/default.aspx)
- [ ] [Azerbaijan / Azərbaycan Mərkəzi Bankı](https://www.cbar.az)
- [x] [Belarus / Natsional'nyy bank Respubliki Belarus'](http://www.nbrb.by/engl/) (driver name 'belarus')
- [x] [Bosnia and Herzegovina / Centralna Banka Bosne I Hergegovine](https://www.cbbh.ba/?lang=en) (driver name 'bosnia-and-herzegovina')
- [x] [Bulgaria / Bŭlgarska narodna banka](http://www.bnb.bg/?toLang=_EN) (driver name 'bulgaria')
- [ ] [Croatia / Hrvatska Narodna Banka](https://www.hnb.hr/home)
- [x] [Czech Republic / Ceska Narodni Banka](https://www.cnb.cz/en/index.html) (driver name 'czech-republic')
- [x] [Danmark / Danmarks Nationalbanks](http://www.nationalbanken.dk/en) (driver name 'denmark')
- [ ] [Georgia / Sakartvelos Erovnuli Bank’i](http://www.nbg.gov.ge)
- [ ] [United Kingdom / Bank of England](https://www.bankofengland.co.uk/)
- [ ] [Hungary / Magyar Nemzeti Bank](https://www.mnb.hu/en/)
- [ ] [Iceland / Seðlabanki Íslands)](https://cb.is)
- [ ] [Liechtenstein / Liechtensteinische Landesbank](https://www.llb.li/en)
- [ ] [Macedonia / Narodna Banka na Republika Severna Makedonija](http://www.nbrm.mk/)
- [ ] [Moldavia / Banca Naţională a Moldovei](http://www.bnm.md/)
- [x] [Norway / Norges Bank](https://www.norges-bank.no/en/)
- [x] [Poland / Narodowy Bank Polski](https://www.nbp.pl/) (driver name 'poland')
- [ ] [Russia / Tsentral'nyy bank Rossiyskoy Federatsii](http://cbr.ru/)
- [x] [Romania / Banca Nationala a Romaniei](https://www.bnro.ro/Home.aspx) (driver name 'romania')
- [ ] [Serbia / Narodna banka Srbije](https://www.nbs.rs/en/indeks/index.html)
- [ ] [Switzerland / Banca naziunala svizra](http://www.snb.ch/)
- [x] [Sweden / Sveriges Riksbank](https://www.riksbank.se/en-gb/) (driver name 'sweden')
- [ ] [Turkey / Türkiye Cumhuriyet Merkez Bankası](http://www.tcmb.gov.tr/)
- [ ] [Ukraine / Natsionalʹnyy bank Ukrayiny](http://www.bank.gov.ua/)

### Asia
- [ ] [China / Zhōngguó Rénmín Yínháng](http://www.pbc.gov.cn/en/3688006/index.html)
- [ ] [India](http://rbi.org.in/)
- [ ] [Indonesia](http://www.bi.go.id/)
- [ ] [Iran / Bank Markazi-ye Jomhuri-ye Eslāmi-ye Irān](http://www.cbi.ir/default_en.aspx)
- [ ] [Iraq / Albank Almarkaziu Aleiraqiu](https://www.cbi.iq/)
- [ ] [Japan / Nippon Ginkō](http://www.boj.or.jp/en/)
- [ ] [Israel / בנק ישראל](https://www.boi.org.il/)
- [ ] [Korea / Hanguk Eunhaeng](http://www.bok.or.kr/eng/)
- [ ] [Lebanon / مصرف لبنان](http://www.bdl.gov.lb/)
- [ ] [Myanmar / မြန်မာနိုင်ငံတော်ဗဟိုဘဏ်](http://www.cbm.gov.mm/)
- [ ] [Nepal / Nepal Rastra Bank](https://www.nrb.org.np/)
- [ ] [Pakistan / بینک دَولتِ پاکِستان](http://www.sbp.org.pk/)
- [ ] [Philippines / Bangko Sentral ng Pilipinas](http://www.bsp.gov.ph/)
- [ ] [Sri Lanka / ශ්‍රී ලංකා මහ බැංකුව](http://www.cbsl.gov.lk/)
- [ ] [Syria / مصرف سورية المركزي](http://cb.gov.sy/en)
- [ ] [the Republic of China (Taiwan) / 中華民國中央銀行](https://www.cbc.gov.tw/en/mp-2.html)
- [ ] [Thailand / ธนาคารแห่งประเทศไทย](http://www.bot.or.th/)
- [ ] [Vietnam / Ngân hàng Nhà nước Việt Nam](http://www.sbv.gov.vn/)
- [ ] [Yemen / البنك الأهلي اليمني](www.nbyemen.com/iNav/index_ar.html)

### Africa
https://en.wikipedia.org/wiki/List_of_banks_in_Africa
- [ ] [Algeria / ]()
- [ ] [Angola / ]()
- [ ] [Benin / ]()
- [ ] [Botswana / ]()
- [ ] [Burkina Faso / ]()
- [ ] [Burundi / ]()
- [ ] [Cameroon / ]()
- [ ] [Cape Verde / ]()
- [ ] [Central African Republic / ]()
- [ ] [Chad / ]()
- [ ] [Comoros / ]()
- [ ] [Democratic Republic of the Congo / ]()
- [ ] [Djibouti / ]()
- [ ] [Egypt / ]()
- [ ] [Equatorial Guinea / ]()
- [ ] [Eritrea / ]()
- [ ] [Ethiopia / ]()
- [ ] [Gabon / ]()
- [ ] [Gambia / ]()
- [ ] [Ghana / ]()
- [ ] [Guinea / ]()
- [ ] [Guinea-Bissau / ]()
- [ ] [Ivory Coast / ]()
- [ ] [Kenya / ]()
- [ ] [Lesotho / ]()
- [ ] [Liberia / ]()
- [ ] [Libya / ]()
- [ ] [Madagascar / ]()
- [ ] [Malawi / ]()
- [ ] [Mali / ]()
- [ ] [Mauritania / ]()
- [ ] [Mauritius / ]()
- [ ] [Morocco / ]()
- [ ] [Mozambique / ]()
- [ ] [Namibia / ]()
- [ ] [Niger / ]()
- [ ] [Nigeria / ]()
- [ ] [Congo / ]()
- [ ] [Rwanda / ]()
- [ ] [São Tomé and Príncipe / ]()
- [ ] [Senegal / ]()
- [ ] [Seychelles / ]()
- [ ] [Sierra Leone / ]()
- [ ] [Somalia / ]()
- [ ] [South Africa / ]()
- [ ] [Sudan / ]()
- [ ] [South Sudan / ]()
- [ ] [Swaziland / ]()
- [ ] [Tanzania / ]()
- [ ] [Togo / ]()
- [ ] [Tunisia / ]()
- [ ] [Uganda / ]()
- [ ] [Zambia / ]()
- [ ] [Zimbabwe / ]()

### North America
- [x] [Canada / Banqueu du Canada](https://www.bankofcanada.ca/) (driver name 'canada')
- [ ] [Antigua and Barbuda]()
- [ ] [Bahamas]()
- [ ] [Barbados]()
- [ ] [Curaçao]()
- [ ] [Cuba]()
- [ ] [Dominica]()
- [ ] [Dominican Republic]()
- [ ] [Grenada]()
- [ ] [Greenland / GrønlandsBANKEN](https://www.banken.gl/en)
- [ ] [Haiti]()
- [ ] [Jamaica]()
- [ ] [Puerto Rico]()
- [ ] [Saint Lucia]()
- [ ] [Trinidad and Tobago]()

### South America
- [ ] [Argentina / Banco Central de la Republica Argentina](http://www.bcra.gob.ar/default.asp)
- [ ] [Belize]()
- [ ] [Bolivia]()
- [ ] [Brasil / Banco Central Do Brasil](https://www.bcb.gov.br/en)
- [ ] [Chile / Banco Central de Chile](https://www.bcentral.cl/en/web/banco-central)
- [ ] [Costa Rica]()
- [ ] [Colombia]()
- [ ] [Ecuador]()
- [ ] [Guyana]()
- [ ] [Nicaragua]()
- [ ] [Panama]()
- [ ] [Paraguay]()
- [ ] [Peru]()
- [ ] [Suriname]()
- [ ] [Uruguay]()
- [ ] [Venezuela]()

### Oceania
- [ ] [Australia / Reserve Bank of Australia](https://www.rba.gov.au/)
- [ ] [Fiji / Reserve Bank of Fiji](http://www.rbf.gov.fj/)
- [ ] [New Zealand / Te Pūtea Matua](http://www.rbnz.govt.nz/index.html)
- [ ] [Papua New Guinea](http://www.bankpng.gov.pg/)
- [ ] [Samoa / Faletupe Tutotonu o Samoa](http://www.cbs.gov.ws/)
- [ ] [Tongo / National Reserve Bank of Tonga](http://www.reservebank.to/)
- [ ] [Vanuatu / Reserve Bank of Vanuatu](http://www.rbv.gov.vu/)

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Krzysztof Bielecki](https://github.com/qwerkon)
- [All Contributors](http://github.com/flexmind-software/currency-rate/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
