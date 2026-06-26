<?php

namespace EdwardTich\FailedJobsManager\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FailedJobsManagerProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/failed-jobs-manager.php' => config_path('failed-jobs-manager.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../../views' => resource_path('views/vendor/failed-jobs-manager'),
            ], 'views');

            $this->publishes([
                __DIR__ . '/../../lang' => lang_path('vendor/failed-jobs-manager'),
            ], 'translations');
        }

        $this->loadTranslationsFrom(
            __DIR__ . '/../../lang',
            'failed-jobs-manager'
        );

        $this->loadViewsFrom(
            __DIR__ . '/../../views',
            'failed-jobs-manager'
        );

        if (config('failed-jobs-manager.ui.enabled')) {
            Route::group(config('failed-jobs-manager.ui.route'), function () {
                $this->loadRoutesFrom(__DIR__ . '/../../routes/failed-jobs-manager.php');
            });
        }
    }

    public function register(): void
    {
        $this->mergeRecursiveConfigFrom(__DIR__ . '/../../config/failed-jobs-manager.php', 'failed-jobs-manager');
    }

    private function mergeRecursiveConfigFrom(string $path, string $key): void
    {
        $config = $this->app['config'];

        $config->set($key, array_replace_recursive(require $path, $config->get($key, [])));
    }
}
