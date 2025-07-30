<?php

namespace ActivityLogger\Providers;

use Illuminate\Support\ServiceProvider;
use ActivityLogger\Middleware\ActivityLoggerMiddleware;
use ActivityLogger\Repositories\ActivityLogRepository;
use ActivityLogger\ActivityLogger;

class ActivityLoggerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/activity-logger.php',
            'activity-logger'
        );

        $this->app->singleton('activity-logger', function ($app) {
            return new ActivityLogger(
                new ActivityLogRepository()
            );
        });

        $this->app->alias('activity-logger', ActivityLogger::class);

        $this->app->singleton(ActivityLogRepository::class, function ($app) {
            return new ActivityLogRepository();
        });
        
        $this->app->singleton(\ActivityLogger\Services\ActivityLogSearchService::class, function ($app) {
            return new \ActivityLogger\Services\ActivityLogSearchService(
                $app->make(ActivityLogRepository::class)
            );
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../config/activity-logger.php' => config_path('activity-logger.php'),
            ], 'activity-logger-config');

            $this->publishes([
                __DIR__ . '/../../database/migrations/' => database_path('migrations'),
            ], 'activity-logger-migrations');
        }

        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        
        // Load views
        $this->loadViewsFrom(__DIR__ . '/../Views', 'activity-logger');
        
        // Publish views for customization
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../Views' => resource_path('views/vendor/activity-logger'),
            ], 'activity-logger-views');
        }
        
        // Load routes if enabled
        if (config('activity-logger.routes.enable_routes', true)) {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        }

        $router = $this->app['router'];
        $router->aliasMiddleware('activity-logger', ActivityLoggerMiddleware::class);

        if (config('activity-logger.auto_register_middleware', false)) {
            $router->pushMiddlewareToGroup('web', ActivityLoggerMiddleware::class);
            $router->pushMiddlewareToGroup('api', ActivityLoggerMiddleware::class);
        }

        $this->registerCommands();
    }

    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \ActivityLogger\Console\Commands\ClearOldLogsCommand::class,
                \ActivityLogger\Console\Commands\ExportLogsCommand::class,
                \ActivityLogger\Console\Commands\AnalyzeLogsCommand::class,
            ]);
        }
    }

    public function provides()
    {
        return [
            'activity-logger',
            ActivityLogger::class,
            ActivityLogRepository::class,
            \ActivityLogger\Services\ActivityLogSearchService::class,
        ];
    }
}