<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel;

use Sirius\Invokator\Callables\CallablePipeline;
use Sirius\Invokator\Laravel\Facades\Invokator;

class FacadeTest extends TestCase
{
    public function test_pipeline_define_returns_the_pipeline_and_run_chains_results(): void
    {
        $pipeline = Invokator::pipeline('p')
            ->add(fn ($x): string => $x . 'a')
            ->add(fn ($x): string => $x . 'b');

        $this->assertInstanceOf(CallablePipeline::class, $pipeline);
        $this->assertSame('xab', Invokator::pipeline('p')->run('x'));
    }

    public function test_bulk_registration_through_the_facade(): void
    {
        Invokator::pipeline('slug', fn ($t): string => trim((string) $t), 'strtolower');

        $this->assertSame('hello', Invokator::pipeline('slug')->run('  HELLO  '));
    }

    public function test_filter_transforms_the_value(): void
    {
        Invokator::filter('up')->add(fn ($v) => strtoupper((string) $v));

        $this->assertSame('HELLO', Invokator::filter('up')->run('hello'));
    }

    public function test_action_runs_for_side_effects_and_returns_null(): void
    {
        $log = [];
        Invokator::action('log')->add(function ($x) use (&$log): void {
            $log[] = "got:$x";
        });

        $result = Invokator::action('log')->run('hi');

        $this->assertNull($result);
        $this->assertSame(['got:hi'], $log);
    }

    public function test_middleware_wraps_with_next(): void
    {
        Invokator::middleware('m')
            ->add(fn ($name, $next = null) => strtoupper((string) $next($name)))
            ->add(fn ($name, $next = null): string => 'Hello ' . $next($name))
            ->add(fn ($name, $next = null) => $name);

        $this->assertSame('HELLO WORLD', Invokator::middleware('m')->run('world'));
    }

    public function test_priority_controls_execution_order(): void
    {
        Invokator::pipeline('ordered')
            ->add(fn ($x): string => $x . '-low', 0)
            ->add(fn ($x): string => $x . '-high', 10);

        // higher priority runs first
        $this->assertSame('start-high-low', Invokator::pipeline('ordered')->run('start'));
    }
}
