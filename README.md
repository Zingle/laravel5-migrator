# Zingle Laravel Migrator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)

This overrides and extends Laravel's own migration service provider, migrate, and rollback commands to provide some additional functionality:

* Adds a 'path' column to the migration tracking table so that the `migrate:rollback` command works properly even when the `--path` option is used when running migrations
* Add improved error display to the `migrate` command, displaying which migrations were succesful and which migration failed in the event of a failed migration

## Install

Via Composer

``` bash
$ composer require zingle/laravel5-migrator
```

Run the migration to add the path column to your migrations table:

`php artisan migrate --path=vendor/zingle/laravel5-migrator/src/migrations`

Add the service provider `Zingle\LaravelMigrator\LaravelMigratorServiceProvider` to your `config/app.php` file `providers` array.



## Usage

This overrides and extends the default Laravel migrate and rollback commands, so just use them as normal.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/:vendor/:package_name.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/zingle/laravel5-migrator
