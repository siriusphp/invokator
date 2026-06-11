<?php

namespace Sirius\Invokator;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    protected $services = [];

    public function get(string $id)
    {
        $service = $this->services[$id] ?? null;
        if (is_callable($service)) {
            return $service();
        }

        return $service;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    public function register(string $id, $implementation): void
    {
        $this->services[$id] = $implementation;
    }
}

class StaticClass
{
    public function method(): void
    {
    }
}

class TestCase extends PHPUnitTestCase
{
    protected Container $container;

    static public $results = [];

    protected function setUp(): void
    {
        parent::setUp();
        static::$results = [];
    }

    protected function getInvoker(): Invoker
    {
        return new Invoker($this->getContainer());
    }

    /**
     * @return mixed[]
     */
    protected function getCallablesFromStack(CallableCollection $stack): array
    {
        $callables = [];
        do {
            $callable = $stack->isEmpty() ? null : $stack->extract();
            if ($callable) {
                $callables[] = $callable;
            }
        } while ($callable);

        return $callables;
    }

    protected function getContainer(): Container
    {
        if ( ! isset($this->container)) {
            $this->container = new Container();
        }

        return $this->container;
    }
}
