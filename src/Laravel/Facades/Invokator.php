<?php

declare(strict_types=1);

namespace Sirius\Invokator\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Sirius\Invokator\Laravel\InvokatorManager;

/**
 * @method static mixed pipeline(string $id, mixed ...$args)
 * @method static mixed action(string $id, mixed ...$args)
 * @method static mixed filter(string $id, mixed ...$args)
 * @method static mixed middleware(string $id, mixed ...$args)
 * @method static \Sirius\Invokator\Laravel\Registrar event(string $eventName)
 * @method static object dispatch(object $event)
 *
 * @see InvokatorManager
 */
class Invokator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'invokator';
    }
}
