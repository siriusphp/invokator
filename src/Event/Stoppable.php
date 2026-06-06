<?php

declare(strict_types=1);

namespace Sirius\Invokator\Event;

/**
 * Public-API convenience trait for consumers' stoppable events; it is used outside
 * the analysed `src/` paths (e.g. by event classes in applications using this library).
 *
 * @phpstan-ignore trait.unused
 */
trait Stoppable
{
    protected bool $propagationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
