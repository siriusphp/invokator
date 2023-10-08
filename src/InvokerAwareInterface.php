<?php
declare(strict_types=1);

namespace Sirius\StackRunner;

interface InvokerAwareInterface
{
    public function setInvoker(Invoker $invoker);
}
