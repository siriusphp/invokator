<?php

declare(strict_types=1);

namespace Sirius\Invokator;

interface InvokatorInterface
{
    /**
     * @param array<mixed> $params
     */
    public function process(string $name, ...$params): mixed;

    /**
     * @param array<mixed> $params
     */
    public function processCollection(CallableCollection $stack, ...$params): mixed;
}
