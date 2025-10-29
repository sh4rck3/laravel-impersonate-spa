<?php

namespace Sh4rck3\LaravelImpersonateSpa\Providers;

use Illuminate\Support\ServiceProvider;

class ImpersonateServiceProvider extends ServiceProvider
{
    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $configPath = __DIR__ . '/../../config/impersonate-spa.php';

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'impersonate-spa');
        }
    }

    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config
        $configPath = __DIR__ . '/../../config/impersonate-spa.php';
        $this->publishes([
            $configPath => config_path('impersonate-spa.php'),
        ], 'impersonate-spa-config');

        // Publish resources (vue components, stubs, etc.) if folder exists
        $resourcesPath = __DIR__ . '/../../resources';
        if (is_dir($resourcesPath)) {
            $this->publishes([
                $resourcesPath => resource_path('vendor/impersonate-spa'),
            ], 'impersonate-spa-resources');
        }

        // Load views if provided
        $viewsPath = __DIR__ . '/../../resources/views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'impersonate-spa');
        }

        // Load routes if provided
        $routesPath = __DIR__ . '/../../routes/web.php';
        if (file_exists($routesPath)) {
            $this->loadRoutesFrom($routesPath);
        }
    }
}
