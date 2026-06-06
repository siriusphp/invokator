<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel;

use Sirius\Invokator\Laravel\Facades\Invokator;
use Sirius\Invokator\Laravel\Registrar;

class FacadeTest extends TestCase
{
    public function test_pipeline_define_returns_registrar_and_run_chains_results(): void
    {
        $registrar = Invokator::pipeline('p')
            ->add(fn ($x): string => $x . 'a')
            ->add(fn ($x): string => $x . 'b');

        $this->assertInstanceOf(Registrar::class, $registrar);
        $this->assertSame('xab', Invokator::pipeline('p', 'x'));
    }

    public function test_filter_transforms_the_value(): void
    {
        Invokator::filter('up')->add(fn ($v) => strtoupper((string) $v));

        $this->assertSame('HELLO', Invokator::filter('up', 'hello'));
    }

    public function test_action_runs_for_side_effects_and_returns_null(): void
    {
        $log = [];
        Invokator::action('log')->add(function ($x) use (&$log): void {
            $log[] = "got:$x";
        });

        $result = Invokator::action('log', 'hi');

        $this->assertNull($result);
        $this->assertSame(['got:hi'], $log);
    }

    public function test_middleware_wraps_with_next(): void
    {
        Invokator::middleware('m')
            ->add(fn ($name, $next = null) => strtoupper((string) $next($name)))
            ->add(fn ($name, $next = null): string => 'Hello ' . $next($name))
            ->add(fn ($name, $next = null) => $name);

        $this->assertSame('HELLO WORLD', Invokator::middleware('m', 'world'));
    }

    public function test_priority_controls_execution_order(): void
    {
        Invokator::pipeline('ordered')
            ->add(fn ($x): string => $x . '-low', 0)
            ->add(fn ($x): string => $x . '-high', 10);

        // higher priority runs first
        $this->assertSame('start-high-low', Invokator::pipeline('ordered', 'start'));
    }
}
