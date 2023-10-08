<?php
declare(strict_types=1);

namespace Sirius\StackRunner;

interface StackRegistryInterface
{
    public function get(string $name): Stack;
}
