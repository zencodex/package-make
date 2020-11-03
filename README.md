# create separate laravel package/plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zencodex/package-make.svg?style=flat-square)](https://packagist.org/packages/zencodex/package-make)
[![Build Status](https://img.shields.io/travis/zencodex/package-make/master.svg?style=flat-square)](https://travis-ci.org/zencodex/package-make)
[![Quality Score](https://img.shields.io/scrutinizer/g/zencodex/package-make.svg?style=flat-square)](https://scrutinizer-ci.com/g/zencodex/package-make)
[![Total Downloads](https://img.shields.io/packagist/dt/zencodex/package-make.svg?style=flat-square)](https://packagist.org/packages/zencodex/package-make)

You can manage huge project with many separate laravel packages.

Thanks to `nwidart/laravel-modules`, I get many code from it and reimplement.

**Why I re-implement (don't use `nwidart/laravel-modules`)?**

1. Too many commands that I have never used.
2. Just required in devepopment, should remove it in production.
3. `nwidart/laravel-modules` stubs injected `module_path`, you can't remove it in production.
4. Laravel SerivceProvider should be enought, don't need `nwidart/laravel-modules` to manage modules.

So I seperate new one `zencodex/package-make`, resovled above issues.

## Installation

You can install the package via composer:

```bash
composer require --dev zencodex/package-make
```

## Usage

``` php
// Modules/NewPackage
php artisan package:make NewPackage
```

NewPackage structure:

```
Modules/NewPackage
├── Config
│   └── config.php
├── Console
├── Database
│   ├── Migrations
│   ├── Seeders
│   │   └── NewPackageDatabaseSeeder.php
│   └── factories
├── Entities
├── Http
│   ├── Controllers
│   │   └── NewPackageController.php
│   ├── Middleware
│   └── Requests
├── Providers
│   ├── NewPackageServiceProvider.php
│   └── RouteServiceProvider.php
├── Resources
│   ├── assets
│   │   ├── js
│   │   │   └── app.js
│   │   └── sass
│   │       └── app.scss
│   ├── lang
│   └── views
│       ├── index.blade.php
│       └── layouts
│           └── master.blade.php
├── Routes
│   ├── api.php
│   └── web.php
├── Tests
│   ├── Feature
│   └── Unit
├── composer.json
├── package.json
└── webpack.mix.js

```

## Usage in project

option 1:

```php
// app/Providers/AppServiceProvider.php

use Modules\NewPackage\Providers\NewPackageServiceProvider;

class AppServiceProvider extends ServiceProvider

    public function register()
    {
        $this->app->register(NewPackageServiceProvider::class);
        ...
    }
```

option 2:

```.json
// 1. edit composer.json, add following
"repositories": [
    {
        "type": "path",
        "url": "Modules/*"
    }
]

// use private package or gitlab
"repositories": [
    {
        "type": "vcs",
        "url": "git@gitlab.example.com:/newpackage.git"
    }
]

// 2. composer require local path package (replace package/newpackage to yours)
composer require package/newpackage
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email v@yinqisen.cn instead of using the issue tracker.

## Credits

- [禅师](https://github.com/zencodex)
- [All Contributors](../../contributors)

## License

The Apache License 2. Please see [License File](LICENSE.md) for more information.

