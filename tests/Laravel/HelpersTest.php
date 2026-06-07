<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel;

use Sirius\Invokator\Callables\CallablePipeline;

class HelpersTest extends TestCase
{
    public function test_do_pipeline_defines_and_runs(): void
    {
        $pipeline = do_pipeline('p')
            ->add(fn ($x): string => $x . 'a')
            ->add(fn ($x): string => $x . 'b');

        $this->assertInstanceOf(CallablePipeline::class, $pipeline);
        $this->assertSame('xab', do_pipeline('p', 'x'));
    }

    public function test_do_filter_transforms_the_value(): void
    {
        do_filter('up')->add(fn ($v) => strtoupper((string) $v));

        $this->assertSame('HELLO', do_filter('up', 'hello'));
    }

    public function test_do_action_runs_side_effects(): void
    {
        $log = [];
        do_action('log')->add(function ($x) use (&$log): void {
            $log[] = $x;
        });

        $this->assertNull(do_action('log', 'hi'));
        $this->assertSame(['hi'], $log);
    }

    public function test_do_middleware_wraps_with_next(): void
    {
        do_middleware('m')
            ->add(fn ($name, $next = null) => strtoupper((string) $next($name)))
            ->add(fn ($name, $next = null): string => 'Hello ' . $next($name))
            ->add(fn ($name, $next = null) => $name);

        $this->assertSame('HELLO WORLD', do_middleware('m', 'world'));
    }
}
