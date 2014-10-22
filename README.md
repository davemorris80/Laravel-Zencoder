
> **PLEASE NOTE:** This is a copy of the repo found here - https://gitlab.advantage4me.co/opensource/zencoder/. I created this 'fork' for use in my own projects as the previous version hasn't been updated to support Laravel 4.2




# Zencoder

A laravel wrapper around the Zencoder API (Current version: v2.2.*)

## Table of Contents

- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Fetcher](#fetcher)

## Installation

You can install the package for your Laravel 4 project through Composer.

Require the package in your `composer.json`.

```
"a4m/zencoder": "1.*"
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

## Fetcher
Zencoder comes with a [nice little ruby gem](https://app.zencoder.com/docs/guides/advanced-integration/getting-zencoder-notifications-while-developing-locally) that acts as a proxy between their server and your local dev environment which is useful if you are developing behind a firewall since
Zencoder cannot access your server to send you the notifications. The issue here of course is that it requires ruby to be installed for it to work.

Being that we are developing with Laravel we might as well leverage the power of PHP so to the rescue comes `php artisan zencoder:notifications`. Which is a php port of the ruby gem.
The port includes all the options that the ruby gem does which can be seen using `php artisan zencoder:notifications --help` or the detailed documentation via `php artisan zencoder:notifications --docs`.

For the command to work you are required to setup the `receivingUrl` in the package configuration file along with the above settings.

Package provided by [Advantage4me™](http://advantage4me.co/) for the OSS community.

