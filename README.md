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
php artisan vendor:publish --provider="FlexMindSoftware\CurrencyRate\SkeletonServiceProvider" --tag="currency-rate-migrations"
php artisan migrate
```

You can publish the config file with:
```bash
php artisan vendor:publish --provider="FlexMindSoftware\CurrencyRate\SkeletonServiceProvider" --tag="currency-rate-config"
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

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Credits

- [Krzysztof Bielecki](https://github.com/qwerkon)
- [All Contributors](http://github.com/flexmind-software/currency-rate/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
