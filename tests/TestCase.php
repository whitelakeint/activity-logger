<?php

namespace ActivityLogger\Tests;

use ActivityLogger\Providers\ActivityLoggerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            ActivityLoggerServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'ActivityLogger' => \ActivityLogger\Facades\ActivityLogger::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $app['config']->set('activity-logger.enabled', true);
    }
}