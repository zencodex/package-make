<?php

namespace Laravel\Package;

use Laravel\Package\Support\Stub;
use Illuminate\Support\ServiceProvider;
use Laravel\Package\Commands\MakeCommand;
use Laravel\Package\Commands\SeedMakeCommand;
use Laravel\Package\Commands\TestMakeCommand;
use Laravel\Package\Commands\ModelMakeCommand;
use Laravel\Package\Commands\CommandMakeCommand;
use Laravel\Package\Commands\ProviderMakeCommand;
use Laravel\Package\Commands\MigrationMakeCommand;
use Laravel\Package\Commands\ControllerMakeCommand;
use Laravel\Package\Commands\RouteProviderMakeCommand;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->commands([
            MakeCommand::class,
            SeedMakeCommand::class,
            ProviderMakeCommand::class,
            ControllerMakeCommand::class,
            CommandMakeCommand::class,
            RouteProviderMakeCommand::class,
            MigrationMakeCommand::class,
            ModelMakeCommand::class,
            TestMakeCommand::class,
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(dirname(__DIR__) . '/config/config.php', 'modules');

        $path = config('modules.stubs.path') ?? __DIR__ . '/Commands/stubs';
        Stub::setBasePath($path);

        $this->app->singleton(Manager::class);
        $this->app->alias(Manager::class, 'modules');
    }

}
