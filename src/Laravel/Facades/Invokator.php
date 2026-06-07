<?php

declare(strict_types=1);

namespace Sirius\Invokator\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Sirius\Invokator\Callables\CallablePipeline pipeline(string $id, mixed ...$callables)
 * @method static \Sirius\Invokator\Callables\CallableAction action(string $id, mixed ...$callables)
 * @method static \Sirius\Invokator\Callables\CallableFilter filter(string $id, mixed ...$callables)
 * @method static \Sirius\Invokator\Callables\CallableMiddleware middleware(string $id, mixed ...$callables)
 * @method static \Sirius\Invokator\Callables\CallableEvent event(string $eventName, mixed ...$listeners)
 * @method static \Sirius\Invokator\Callables\CallableCommand command(string $commandClass, mixed ...$middleware)
 * @method static object dispatch(object $event)
 * @method static mixed handle(object $command)
 *
 * @see \Sirius\Invokator\Invokator
 */
class Invokator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'invokator';
    }
}
