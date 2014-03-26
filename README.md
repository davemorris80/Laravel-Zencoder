# Zencoder

A laravel wrapper around the Zencoder API (Current version: v2.1.*)

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)

## Installation

You can install the package for your Laravel 4 project through Composer.

Require the package in your `composer.json`.

```
"a4m/zencoder": "1.0.*"
```

Run composer to install or update the package.

```bash
$ composer update
```

Register the service provider in `app/config/app.php`.

```php
'A4M\Zencoder\ZencoderServiceProvider',
```

Add the alias to the list of aliases in `app/config/app.php`.

```php
'Zencoder' => 'A4M\Zencoder\ZencoderFacade',
```

## Configuration

The packages provides you with some configuration **options that are required**.

To create the configuration file run this command in your command line app:

```bash
$ php artisan config:publish a4m/zencoder
```

The configuration file will be published here: `app/config/packages/a4m/zencoder/config.php`.

## Usage

This is a simple wrapper around the official api, which means that everything you can do via [documented on the official API](https://github.com/zencoder/zencoder-php) can be done through ```Zencoder::``` with one exception; Laravel facades do not allow us direct access to the properties so we use accessor functions e.g.

```php
Zencoder::accounts()->create($array);
Zencoder::jobs()->create($array);
Zencoder::jobs()->progress($job_id);
Zencoder::inputs()->details($input_id);
Zencoder::outputs()->details($output_id);
Zencoder::notifications()->parseIncoming();
```

We also have one more function `Zencoder::instance()` which simply returns the Zencoder instance allowing these properties to be set e.g.

```php
Zencoder::instance()->jobs->create($array);
Zencoder::instance()->jobs = 'Something Else';
```