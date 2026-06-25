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
        }

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
        if ( ! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(
                __DIR__ . '/../../config/failed-jobs-manager.php',
                'failed-jobs-manager'
            );
        }
    }
}
