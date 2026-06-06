---
title: Upgrading Sirius\Invokator from 1.x to 2.0
---

# Upgrading to 2.0

Version 2.0 modernizes the library for current PHP versions and refreshes the
development tooling. The public API is unchanged, so for most applications the
upgrade is just a version bump. There are two things to be aware of.

## 1. PHP 8.3 or newer is required

The minimum supported PHP version moved from 8.0 to **8.3**. The library is tested
against PHP 8.3, 8.4 and 8.5.

If you are still on PHP 8.0–8.2, stay on the `1.x` releases until you can upgrade
your runtime.

```bash
composer require siriusphp/invokator:^2.0
```

## 2. Value objects are now immutable (`readonly`)

The small value objects used internally now declare their public properties as
`readonly`:

- `ArgumentReference` — created by `arg()`
- `InvokerReference` — created by `ref()`
- `InvokerResult` — created by `result_of()`
- `PipelinePromise`
- `SuggestedResume`
- `SuggestedRetry`

In practice you create these through the helper functions and read their
properties, so nothing changes. The only breaking case is code that **reassigned**
one of their public properties after construction:

```php
$ref = arg(0);
$ref->reference = 1; // 2.0: Error — cannot modify readonly property
```

Build a new instance instead of mutating an existing one:

```php
$ref = arg(1);
```

## Tooling changes (development only)

These do not affect applications consuming the library; they matter only if you
work on the library itself or copy its setup:

- PHPUnit `9` → `12` (test configuration migrated to the 12.x schema).
- PHPStan `1` → `2` (still analysed at level 9).
- [Rector](https://getrector.com/) `2` added; run `composer run rector`.
- `friendsofphp/php-cs-fixer` moved from `tools/` into `require-dev`; `composer run
  csfix` now applies the `@PSR12` ruleset.
- The bundled Docker image is based on `php:8.3-fpm`.
