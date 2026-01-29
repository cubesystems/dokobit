<?php

declare(strict_types=1);

namespace Dokobit\Tests;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Spatie\LaravelData\LaravelDataServiceProvider;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the application is set globally for helpers like config()
        if ($this->app instanceof Application) {
            Application::setInstance($this->app);

            // Also set it in the Container base class
            Container::setInstance($this->app);
        }
    }

    protected function getBasePath(): string
    {
        return __DIR__ . '/..';
    }

    protected function getPackageProviders($app): array
    {
        return [
            LaravelDataServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        // Setup default environment configuration
    }
}
