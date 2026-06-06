<?php

declare(strict_types=1);

namespace Sirius\Invokator\Modifiers;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Sirius\Invokator\ArgumentReference;
use Sirius\Invokator\Invoker;
use Sirius\Invokator\InvokerAwareInterface;

class ResolveArguments implements InvokerAwareInterface
{
    protected Invoker $invoker;

    /**
     * @param array<mixed> $arguments
     */
    // $arguments is intentionally NOT readonly: __invoke() resolves ArgumentReferences
    // into it in place.
    public function __construct(public readonly mixed $callable, public array $arguments)
    {
    }

    public function setInvoker(Invoker $invoker): void
    {
        $this->invoker = $invoker;
    }

    /**
     * @param array<mixed> $params
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
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
