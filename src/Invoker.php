<?php

declare(strict_types=1);

namespace Sirius\Invokator;

use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

class Invoker
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array<int, mixed> $params
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function invoke(mixed $callable, ...$params): mixed
    {
        $args = $this->computeArguments($params);

        $callable = $this->getActualCallable($callable);

        if ($callable instanceof InvokerAwareInterface) {
            $callable->setInvoker($this);
        }

        return $callable(...$args);
    }
    /**
     * @param array<string, mixed> $params
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function invokeWithNamedArguments(mixed $callable, array $params): mixed
    {
        $callable = $this->getActualCallable($callable);
        $args = $this->resolveArguments($callable, $params);

        return $this->invoke($callable, ...$args);
    }

    /**
     * @param array<mixed> $params
     *
     * @return array<mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function computeArguments(array $params): array
    {
        $values = [];
        foreach ($params as $p) {
            if ($p instanceof InvokerReference) {
                $values[] = $this->container->get($p->reference);
            } elseif ($p instanceof InvokerResult) {
                $args     = $p->params;
                $values[] = $this->invoke($p->callable, ...$args);
            } else {
                $values[] = $p;
            }
        }

        return $values;
    }

    /**
     * @param array<string, mixed> $args
     *
     * @return array<mixed>
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \ReflectionException
     */
    protected function resolveArguments(mixed $callable, array $args): array
    {
        if (is_string($callable) && is_callable($callable)) {
            $reflection = new ReflectionFunction($callable);
        } elseif (is_array($callable) && is_callable($callable)) {
            $reflection = new ReflectionMethod($callable[0], $callable[1]);
        } else {
            throw new InvalidArgumentException('Invalid callable provided');
        }

        $resolvedArgs = [];

        /** @var ReflectionParameter $param */
        foreach ($reflection->getParameters() as $param) {
            $paramName = $param->getName();
            /** @var ?ReflectionNamedType $paramType */
            $paramType = $param->getType();
            if ($paramType && !$paramType->isBuiltin()) {
                if ($this->container->has($paramType->getName())) {
                    $resolvedArgs[] = $this->container->get($paramType->getName());
                } else {
                    throw new InvalidArgumentException("Cannot resolve parameter: $paramName");
                }
            } else if (isset($args[$paramName]) || $param->getDefaultValue()) {
                // Builtin types, such as int or string, do not need resolution.
                $resolvedArgs[] = $args[$paramName] ?? $param->getDefaultValue();
            } else {
                throw new InvalidArgumentException("Cannot resolve parameter: $paramName");
            }
        }

        return $resolvedArgs;
    }

    /**
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws InvalidCallableException
     */
    protected function getActualCallable(mixed $callable): callable
    {
        if (is_string($callable) && str_contains($callable, '@')) {
            [$service, $method] = explode('@', $callable, 2);
            $service = $this->container->get($service);
            if ($service instanceof InvokerAwareInterface) {
                $service->setInvoker($this);
            }
            $callable = [$service, $method];
        }

        // if the callable references an invokable class from the container
        if (is_string($callable) &&
            is_callable($callable) &&
            ($service = $this->container->get($callable)) &&
            is_callable($service)
        ) {
            $callable = $service;
        }

        if (!is_callable($callable)) {
            throw new InvalidCallableException();
        }

        return $callable;
    }
}
