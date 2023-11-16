# Doc Shamer

[![Latest Version on Packagist](https://img.shields.io/packagist/v/thewoods96/doc-shamer.svg?style=flat-square)](https://packagist.org/packages/thewoods96/doc-shamer)
[![Total Downloads](https://img.shields.io/packagist/dt/thewoods96/doc-shamer.svg?style=flat-square)](https://packagist.org/packages/thewoods96/doc-shamer)

A basic Laravel Artisan command to check an OpenAPI spec against application API routes to summarise doc coverage.

## Installation

You can install the package via composer:

```bash
composer require thewoods96/doc-shamer
```

## Usage

**Usage:**
```bash
$ php artisan doc-shamer [options] [arguments]
```
#### Options:

--show-coverage: Output tables detailing documented, missing and ignored routes

--dry-run: When set to true the command will always exit with status 0 regardless of doc coverage.


### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email 96woods96@gmail.com instead of using the issue tracker.

## Credits

-   [Connor Woods](https://github.com/thewoods96)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
