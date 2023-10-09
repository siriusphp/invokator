---
title: Custom callable stack locators
---

# Custom stack locator

You can easily build your own custom locator by extending the `SimpleStackSelector` or starting from scratch as the API for a stack locator is very simple. 

If you extend the `SimpleStackLocator` you only need to implement the `processStack()` method.

Here are some ideas:
1. pipelines where all the callbacks receive the same arguments and where the result of a callback becomes the first argument in the list. It would be similar to the "Filters locator" but without having to specify the limit for the arguments.

[Next: callable modifiers](3_callable_modifiers.md)
