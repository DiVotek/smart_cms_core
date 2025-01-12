<?php

namespace SmartCms\Core\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use SmartCms\Core\SmartCmsPanelManager;
use SmartCms\Core\SmartCmsServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'SmartCms\\Core\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            SmartCmsServiceProvider::class,
            SmartCmsPanelManager::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:n5LulPjYL3WqUwnQm+o5aE+BPEQz/uhS26u+gStO7kY=');
        config()->set('database.default', 'testing');
        $app->singleton('_settings', function () {
            return new \SmartCms\Core\Services\Singletone\Settings;
        });
        $app->singleton('_lang', function () {
            return new \SmartCms\Core\Services\Singletone\Languages;
        });
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}
