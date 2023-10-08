<?php
declare(strict_types=1);

namespace Sirius\StackRunner;

class PipelinePromise
{
    public function __construct(public $value, public Stack $remainingStack, public array $params, public $retryAfter)
    {
    }
}
