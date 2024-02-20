<?php

namespace EscolaLms\BulkNotifications;

use EscolaLms\Auth\EscolaLmsAuthServiceProvider;
use EscolaLms\Settings\EscolaLmsSettingsServiceProvider;
use Illuminate\Support\ServiceProvider;

/**
 * SWAGGER_VERSION
 */
class EscolaLmsBulkNotificationsServiceProvider extends ServiceProvider
{
    const CONFIG_KEY = 'escolalms_bulk_notifications';

    public const REPOSITORIES = [];

    public const SERVICES = [];

    public $singletons = self::SERVICES + self::REPOSITORIES;

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', self::CONFIG_KEY);

        $this->app->register(EscolaLmsSettingsServiceProvider::class);
        $this->app->register(EscolaLmsAuthServiceProvider::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    public function bootForConsole()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/config.php' => config_path(self::CONFIG_KEY . '.php'),
        ], self::CONFIG_KEY . '.config');
    }
}
