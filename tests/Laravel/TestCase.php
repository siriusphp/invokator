<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel;

use Orchestra\Testbench\TestCase as Orchestra;
use Sirius\Invokator\Laravel\Facades\Invokator;
use Sirius\Invokator\Laravel\SiriusInvokatorServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [SiriusInvokatorServiceProvider::class];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return ['Invokator' => Invokator::class];
    }
}
