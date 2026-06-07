<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel;

use Sirius\Invokator\Laravel\Facades\Invokator;
use Sirius\Invokator\Tests\Laravel\Fixtures\Greeter;

class ContainerWiringTest extends TestCase
{
    public function test_string_callable_is_resolved_from_the_laravel_container_with_dependency_injection(): void
    {
        // `Greeter@greet` is resolved by the Invoker through Laravel's container, which
        // injects the Greeter's own constructor dependency (Dependency). This proves the
        // PSR-11 wiring done by the service provider.
        Invokator::filter('greet')->add(Greeter::class . '@greet');

        $this->assertSame('Hello, Sam', Invokator::filter('greet')->run('Sam'));
    }

    public function test_plain_function_name_string_callable_works_under_the_laravel_container(): void
    {
        // Laravel's container throws for unbound ids; the Invoker must fall through to using
        // 'trim' directly rather than asking the container to resolve it.
        Invokator::filter('clean')->add('trim');

        $this->assertSame('hi', Invokator::filter('clean')->run('  hi  '));
    }
}
