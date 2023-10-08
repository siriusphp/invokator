<?php
declare(strict_types=1);

namespace Sirius\StackRunner;

interface StackRunnerInterface
{
    public function process(string $name, ...$params);

    public function processStack(Stack $stack, ...$params);
}
