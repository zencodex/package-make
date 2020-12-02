<?php

namespace Laravel\Package;

use Laravel\Package\Support\Stub;
use Illuminate\Support\ServiceProvider;
use Laravel\Package\Commands\MakeCommand;
use Laravel\Package\Commands\JobMakeCommand;
use Laravel\Package\Commands\SeedMakeCommand;
use Laravel\Package\Commands\TestMakeCommand;
use Laravel\Package\Commands\MailMakeCommand;
use Laravel\Package\Commands\RuleMakeCommand;
use Laravel\Package\Commands\ModelMakeCommand;
use Laravel\Package\Commands\EventMakeCommand;
use Laravel\Package\Commands\CommandMakeCommand;
use Laravel\Package\Commands\FactoryMakeCommand;
use Laravel\Package\Commands\ProviderMakeCommand;
use Laravel\Package\Commands\ResourceMakeCommand;
use Laravel\Package\Commands\ListenerMakeCommand;
use Laravel\Package\Commands\MigrationMakeCommand;
use Laravel\Package\Commands\ControllerMakeCommand;
use Laravel\Package\Commands\MiddlewareMakeCommand;
use Laravel\Package\Commands\NotificationMakeCommand;
use Laravel\Package\Commands\RouteProviderMakeCommand;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {

        $this->commands([
            CommandMakeCommand::class,
            ControllerMakeCommand::class,
            EventMakeCommand::class,
            FactoryMakeCommand::class,
            JobMakeCommand::class,
            ListenerMakeCommand::class,
            MailMakeCommand::class,
            MakeCommand::class,
            MiddlewareMakeCommand::class,
            MigrationMakeCommand::class,
            NotificationMakeCommand::class,
            ModelMakeCommand::class,
            ProviderMakeCommand::class,
            ResourceMakeCommand::class,
            ResourceMakeCommand::class,
            RouteProviderMakeCommand::class,
            RuleMakeCommand::class,
            SeedMakeCommand::class,
            TestMakeCommand::class,
        ]);

        $this->publishes([
            dirname(__DIR__, 1) . '/config/config.php' => config_path('package.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $publishedConfig = config_path('package.php');
        if (file_exists($publishedConfig)) {
            $this->mergeConfigFrom($publishedConfig, 'modules');
        } else {
            $this->mergeConfigFrom(dirname(__DIR__) . '/config/config.php', 'modules');
        }

        $path = config('modules.stubs.path') ?? __DIR__ . '/Commands/stubs';
        Stub::setBasePath($path);

        $this->app->singleton(Manager::class);
        $this->app->alias(Manager::class, 'modules');
    }

}
