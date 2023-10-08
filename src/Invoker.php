<?php
declare(strict_types=1);

namespace Sirius\StackRunner;

use Psr\Container\ContainerInterface;

class Invoker
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function invoke($callable, ...$params): mixed
    {
        $args = $this->resolveArguments($params);

        if (is_string($callable) && str_contains($callable, '@')) {
            [$service, $method] = explode('@', $callable, 2);
            if ($service instanceof InvokerAwareInterface) {
                $service->setInvoker($this);
            }
            $callable = [$this->container->get($service), $method];
        }

        if ($callable instanceof InvokerAwareInterface) {
            $callable->setInvoker($this);
        }

        return $callable(...$args);
    }

    protected function resolveArguments(array $params): array
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
}
