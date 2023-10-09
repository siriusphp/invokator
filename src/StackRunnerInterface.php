<?php

declare(strict_types=1);

namespace Sirius\StackRunner;

interface StackRunnerInterface
{
    /**
     * @param array<mixed> $params
     */
    public function process(string $name, ...$params): mixed;

    /**
     * @param array<mixed> $params
     */
    public function processStack(Stack $stack, ...$params): mixed;
}
