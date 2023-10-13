<?php

declare(strict_types=1);

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\ArgumentReference;
use Sirius\Invokator\Invoker;
use Sirius\Invokator\InvokerAwareInterface;
use Sirius\Invokator\InvokerReference;

class ResolveArguments implements InvokerAwareInterface
{
    protected Invoker $invoker;

    /**
     * @param array<mixed> $arguments
     */
    public function __construct(public mixed $callable, public array $arguments)
    {
    }

    public function setInvoker(Invoker $invoker): void
    {
        $this->invoker = $invoker;
    }

    /**
     * @param array<mixed> $params
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(...$params): mixed
    {
        $params = $this->invoker->computeArguments($params);
        foreach ($this->arguments as $name => $value) {
            if ($value instanceof ArgumentReference) {
                $this->arguments[$name] = $params[$value->reference];
            }
        }
        return $this->invoker->invokeWithNamedArguments($this->callable, $this->arguments);
    }
}
