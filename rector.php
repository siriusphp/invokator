<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Php81\Rector\Property\ReadOnlyPropertyRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // Resolve the PHP feature level from composer.json ("php": "^8.3"),
    // so emitted syntax always runs on the lowest supported version.
    ->withPhpSets()
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        earlyReturn: true,
    )
    // `readonly` on public value-object properties is a minor BC break, so it is
    // applied deliberately by hand (see the value objects in src/) rather than
    // swept in automatically.
    ->withSkip([
        ReadOnlyPropertyRector::class,
        // $reversed must keep its class-level default for __unserialize() (which
        // bypasses the constructor), so it cannot be a promoted property.
        ClassPropertyAssignToConstructorPromotionRector::class => [
            __DIR__ . '/src/CallableCollection.php',
        ],
    ]);
