[![MIT License](http://img.shields.io/badge/license-MIT-green.svg) ](https://github.com/tinify/tinify-php/blob/main/LICENSE)
[![CI_CD](https://github.com/tinify/tinify-php/actions/workflows/ci-cd.yaml/badge.svg?branch=master)](https://github.com/tinify/tinify-php/actions/workflows/ci-cd.yaml)
[![Packagist Version](https://img.shields.io/packagist/v/tinify/tinify)](https://packagist.org/packages/tinify/tinify)
[![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/tinify/tinify)](https://packagist.org/packages/tinify/tinify)

# Tinify API client for PHP

PHP client for the Tinify API, used for [TinyPNG](https://tinypng.com) and [TinyJPG](https://tinyjpg.com). Tinify compresses your images intelligently. Read more at [http://tinify.com](http://tinify.com).

## Documentation

[Go to the documentation for the PHP client](https://tinypng.com/developers/reference/php).

## Installation

Install the API client with Composer. Add this to your `composer.json`:

```json
{
  "require": {
    "tinify/tinify": "*"
  }
}
```

Then install with:

```
composer install
```

Use autoloading to make the client available in PHP:

```php
require_once("vendor/autoload.php");
```

## Usage

```php
Tinify\setKey("YOUR_API_KEY");
Tinify\fromFile("unoptimized.png")->toFile("optimized.png");
```

## Running tests

```
composer install
vendor/bin/phpunit
```

### Integration tests

```
composer install
TINIFY_KEY=$YOUR_API_KEY vendor/bin/phpunit --no-configuration test/integration.php
```

## License

This software is licensed under the MIT License. [View the license](LICENSE).
