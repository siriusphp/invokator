<?php

declare(strict_types=1);

namespace Sirius\Invokator;

interface CallablesRegistryInterface
{
    public function get(string $name): CallableCollection;
}
