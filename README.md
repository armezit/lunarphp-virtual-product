# Lunar Virtual/Digital Product Addon

[![Latest Version on Packagist](https://img.shields.io/packagist/v/armezit/lunarphp-virtual-product.svg?style=flat-square)](https://packagist.org/packages/armezit/lunarphp-virtual-product)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/armezit/lunarphp-virtual-product/run-tests.yml?branch=main&label=tests)](https://github.com/armezit/lunarphp-virtual-product/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/armezit/lunarphp-virtual-product/fix-php-code-style-issues.yml?branch=main&label=code%20style)](https://github.com/armezit/lunarphp-virtual-product/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/armezit/lunarphp-virtual-product.svg?style=flat-square)](https://packagist.org/packages/armezit/lunarphp-virtual-product)

Virtual products (also known as digital goods), represent non-tangible items such as memberships, services, warranties, 
subscriptions and digital downloadable goods like games, virtual game tokens, books, music, videos, or other products.

The Virtual Product addon for Lunar allows you to define virtual/digital products in your
[Lunar](https://github.com/lunarphp/lunar) store.

## Requirements

- PHP ^8.1
- Laravel 9+

## Quick Setup

You can install the package via composer:

```bash
composer require armezit/lunarphp-virtual-product
```

Run the migrations with:

```bash
php artisan migrate
```

## Usage

_TBD._

## Installation

[Quick Setup](#quick-setup) covers the essential installation steps.
This section, however, is a detailed installation procedure,
containing all optional parts.

You can install the package via composer:

```bash
composer require armezit/lunarphp-virtual-product
```

### Migrations

Publish the migrations and run them with:

```bash
php artisan vendor:publish --tag="lunarphp-virtual-product-migrations"
php artisan migrate
```

::: tip Table names are configurable. See the config file. :::

### Config

You can publish the config file with:

```bash
php artisan vendor:publish --tag="lunarphp-virtual-product-config"
```

This is the contents of the published config file:

```php
return [
    
];
```

### Translations & Views

Optionally, you can publish the translations and views using

```bash
php artisan vendor:publish --tag="lunarphp-virtual-product-translations"
php artisan vendor:publish --tag="lunarphp-virtual-product-views"
```

### Service provider

By default, this package automatically register it\`s service providers when it is installed.

If for any reason you prefer to register them manually, you should add the package service providers
into your laravel application's `config/app.php` file.

```php
// ...
'providers' => [
    // ...
    Armezit\Lunar\VirtualProduct\VirtualProductServiceProvider::class,
    Armezit\Lunar\VirtualProduct\VirtualProductHubServiceProvider::class,
],
```

The `VirtualProductServiceProvider` bootstrap primary package features,
while the `VirtualProductHubServiceProvider` is used to register some
[Slots](https://docs.lunarphp.io/extending/admin-hub.html#slots) to be used in Lunar Admin Hub.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/armezit/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Armin Rezayati](https://github.com/armezit)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
