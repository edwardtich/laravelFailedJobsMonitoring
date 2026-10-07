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
        if ($this->app->configurationIsCached()) {
            return;
        }

        $config = $this->app['config'];
        $key = 'failed-jobs-manager';

        $config->set($key, $this->mergeConfig(
            require __DIR__ . '/../../config/failed-jobs-manager.php',
            $config->get($key, []),
        ));
    }

    /**
     * Associative arrays are merged recursively, lists (middleware, roles) are replaced as a whole.
     *
     * @param array<array-key, mixed> $defaults
     * @param array<array-key, mixed> $overrides
     *
     * @return array<array-key, mixed>
     */
    private function mergeConfig(array $defaults, array $overrides): array
    {
        foreach ($overrides as $key => $value) {
            $default = $defaults[$key] ?? null;

            $defaults[$key] = is_array($value) && is_array($default) && ! array_is_list($value) && ! array_is_list($default)
                ? $this->mergeConfig($default, $value)
                : $value;
        }

        return $defaults;
    }
}
