<?php

namespace Sirius\StackRunner;

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

    public function register(string $id, $implementation)
    {
        $this->services[$id] = $implementation;
    }
}

class StaticClass
{
    function method()
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

    protected function getInvoker()
    {
        return new Invoker($this->getContainer());
    }

    protected function getCallablesFromStack(Stack $stack)
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

    protected function getContainer()
    {
        if ( ! isset($this->container)) {
            $this->container = new Container();
        }

        return $this->container;
    }
}
