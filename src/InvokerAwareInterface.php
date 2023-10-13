<?php

declare(strict_types=1);

namespace Sirius\Invokator;

interface InvokerAwareInterface
{
    public function setInvoker(Invoker $invoker): void;
}
