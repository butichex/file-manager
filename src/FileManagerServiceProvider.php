<?php

namespace dyutin\FileManager;

use Illuminate\Support\ServiceProvider;
use dyutin\FileManager\Contracts\FileServiceInterface;
use dyutin\FileManager\Services\FileService;

class FileManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/file-manager.php', 'file-manager');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'file-manager');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'fileManager');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/file-manager.php' => config_path('file-manager.php'),
            ], 'config');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/file-manager'),
            ], 'views');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/file-manager'),
            ], 'translations');

            $this->publishes([
                __DIR__ . '/../file-manager' => public_path('vendor/file-manager'),
            ], 'public');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->bind(FileServiceInterface::class, static function () {
            $config = config('file-manager.paths');

            return (new FileService($config['base']))
                ->setHidden($config['hidden'] ?? [])
                ->setPathPattern($config['pattern'] ?? '/*');
        });
    }
}
