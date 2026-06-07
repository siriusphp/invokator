---
title: Custom callable stacks
---

# Custom callable stacks

You can easily build your own runner by extending `Sirius\Invokator\Callables\AbstractCallableStack` and implementing the `run(mixed ...$args): mixed` method. The base class gives you the `add()` method, the `$this->invoker` used to execute callables, and a protected `freshStack()` helper that returns a disposable clone of the registered callables (a `CallableCollection` you can drain with `extract()` without altering the runner).

```php
use Sirius\Invokator\Callables\AbstractCallableStack;

class MyRunner extends AbstractCallableStack
{
    public function run(mixed ...$args): mixed
    {
        $stack  = $this->freshStack();
        $result = null;
        while (! $stack->isEmpty()) {
            $callable = $stack->extract();
            $result   = $this->invoker->invoke($callable, ...$args);
        }

        return $result;
    }
}
```

Here are some ideas:
1. A pipeline where all the callbacks receive the same arguments and where the result of a callback becomes the first argument in the list. It would be similar to the `CallableFilter` but without having to specify the argument limit.
2. An HTTP middleware implementation of the PSR-15 standard. It would be similar to the `CallableMiddleware` but with the restriction that all the callables share the same signature.

[Next: callable modifiers](3_callable_modifiers.md)
