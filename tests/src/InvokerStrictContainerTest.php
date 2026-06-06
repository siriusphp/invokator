<?php

declare(strict_types=1);

namespace Sirius\Invokator;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class StrictNotFoundException extends \RuntimeException implements NotFoundExceptionInterface
{
}

/**
 * A strict, spec-compliant PSR-11 container whose get() throws for unbound ids (as Laravel's
 * container does), unlike the lenient test Container that returns null.
 */
class StrictContainer implements ContainerInterface
{
    /** @var array<string, mixed> */
    private array $services = [];

    public function set(string $id, mixed $value): void
    {
        $this->services[$id] = $value;
    }

    public function get(string $id): mixed
    {
        if (! $this->has($id)) {
            throw new StrictNotFoundException("No entry found for: {$id}");
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->services);
    }
}

class Shouter
{
    public static function shout(string $value): string
    {
        return strtoupper($value);
    }
}

class InvokerStrictContainerTest extends PHPUnitTestCase
{
    public function test_plain_function_name_string_callable_is_used_directly(): void
    {
        $invoker = new Invoker(new StrictContainer());

        // 'trim' is callable but not a bound service: get() would throw, so it must be used as-is.
        $this->assertSame('hi', $invoker->invoke('trim', '  hi  '));
    }

    public function test_static_method_string_callable_is_used_directly(): void
    {
        $invoker = new Invoker(new StrictContainer());

        $this->assertSame('HELLO', $invoker->invoke(Shouter::class . '::shout', 'hello'));
    }

    public function test_bound_callable_name_still_resolves_from_the_container(): void
    {
        $container = new StrictContainer();
        // A callable-looking string that is ALSO bound should resolve to the bound service
        // (the has()+get() branch), proving the guard doesn't disable container overrides.
        $container->set('strtoupper', fn (string $value): string => 'OVERRIDDEN:' . $value);
        $invoker = new Invoker($container);

        $this->assertSame('OVERRIDDEN:x', $invoker->invoke('strtoupper', 'x'));
    }
}
