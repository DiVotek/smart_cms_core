# This is my package core

[![Latest Version on Packagist](https://img.shields.io/packagist/v/smart-cms/core.svg?style=flat-square)](https://packagist.org/packages/smart-cms/core)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/smart-cms/core/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/smart-cms/core/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/smart-cms/core.svg?style=flat-square)](https://packagist.org/packages/smart-cms/core)

Smart CMS description.

## Installation

You can install the package via composer:

```bash
composer require smart-cms/core
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="core-migrations"
php artisan migrate
```

## Usage

```php
$core = new SmartCms\Core();
echo $core->echoPhrase('Hello, Smart_cms!');
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [smart_cms](https://github.com/smart_cms)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
