<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use WayOfDev\Auth\Bridge\Laravel\Providers\PackageServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            PackageServiceProvider::class,
        ];
    }
}
