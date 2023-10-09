<?php

declare(strict_types=1);

namespace Sirius\StackRunner;

class InvokerResult
{
    public function __construct(public $callable, public array $params = [])
    {
    }
}
