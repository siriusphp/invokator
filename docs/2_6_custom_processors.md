---
title: Custom callable processors
---

# Custom callables processor

You can easily build your own custom processor by extending the `SimpleCallablesProcessor` or starting from scratch as the API for a callables processor is very simple. 

If you extend the `SimpleCallablesProcessor` you only need to implement the `processCollection()` method.

Here are some ideas:
1. pipelines where all the callbacks receive the same arguments and where the result of a callback becomes the first argument in the list. It would be similar to the "Filters processor" but without having to specify the limit for the arguments.

[Next: callable modifiers](3_callable_modifiers.md)
