<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel\Fixtures;

/**
 * Used by ContainerWiringTest: referenced as the `Greeter@greet` string callable, this is
 * resolved by Laravel's container — which injects {@see Dependency} into the constructor —
 * proving the Invoker is wired to the Laravel container.
 */
class Greeter
{
    public function __construct(private Dependency $dependency)
    {
    }

    public function greet(string $name): string
    {
        return $this->dependency->prefix() . $name;
    }
}
