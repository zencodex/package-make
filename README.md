![](https://gitee.com/zencodex/images/raw/master/package-make.png)

# Create PHP or laravel package/plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/zencodex/package-make.svg?style=flat-square)](https://packagist.org/packages/zencodex/package-make)
[![Build Status](https://img.shields.io/travis/zencodex/package-make/master.svg?style=flat-square)](https://travis-ci.org/zencodex/package-make)
[![Quality Score](https://img.shields.io/scrutinizer/g/zencodex/package-make.svg?style=flat-square)](https://scrutinizer-ci.com/g/zencodex/package-make)
[![Total Downloads](https://img.shields.io/packagist/dt/zencodex/package-make.svg?style=flat-square)](https://packagist.org/packages/zencodex/package-make)

You can manage huge project with many separate laravel packages.

Thanks to `nwidart/laravel-modules`, I get many code from it and reimplement.

**Why I re-implement (don't use `nwidart/laravel-modules`)?**

1. `nwidart/laravel-modules` stubs injected `module_path`, you can't remove it in production.
2. Just a standard composer package, you don't need `nwidart/laravel-modules` to manage modules.
3. Update some stubs and folders structure, keep it like laravel.
4. You can remove this package in production, Just required in devepopment.

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
│   └── UserCommand.php
├── Database
│   ├── Migrations
│   ├── Seeders
│   │   └── NewPackageDatabaseSeeder.php
│   └── factories
├── Http
│   ├── Controllers
│   │   └── NewPackageController.php
│   ├── Middleware
│   ├── Requests
│   └── Resources
│       └── UserResource.php
├── Models
│   └── User.php
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

or 

Edit `config/app.php`, add `Modules\NewPackage\Providers\NewPackageServiceProvider::class` to providers.

```.php

    'providers' => [
        Illuminate\Validation\ValidationServiceProvider::class,
        Illuminate\View\ViewServiceProvider::class,
        Modules\NewPackage\Providers\NewPackageServiceProvider::class
        ...
    ],

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

