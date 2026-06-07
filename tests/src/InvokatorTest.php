<?php

namespace Sirius\Invokator;

use Sirius\Invokator\Callables\CallablePipeline;
use Sirius\Invokator\CommandBus\SampleCommand;
use Sirius\Invokator\CommandBus\SampleHandler;
use Sirius\Invokator\Event\EventWithoutName;
use Sirius\Invokator\Event\StoppableEvent;

require_once __DIR__ . '/Event/EventWithoutName.php';
require_once __DIR__ . '/Event/StoppableEvent.php';

class InvokatorTest extends TestCase
{
    private function getInvokator(): Invokator
    {
        return new Invokator($this->getInvoker());
    }

    public function test_pipeline_bulk_register_and_run(): void
    {
        $invokator = $this->getInvokator();
        $invokator->pipeline('slug', fn ($t): string => trim((string) $t), 'strtolower');

        $this->assertSame('hello', $invokator->pipeline('slug')->run('  HELLO  '));
    }

    public function test_pipeline_is_cached_per_identifier(): void
    {
        $invokator = $this->getInvokator();

        $first  = $invokator->pipeline('p');
        $second = $invokator->pipeline('p');

        $this->assertInstanceOf(CallablePipeline::class, $first);
        $this->assertSame($first, $second);
    }

    public function test_registrations_accumulate_across_calls(): void
    {
        $invokator = $this->getInvokator();
        $invokator->pipeline('p')->add(fn ($x): string => $x . 'a');
        $invokator->pipeline('p')->add(fn ($x): string => $x . 'b');

        $this->assertSame('xab', $invokator->pipeline('p')->run('x'));
    }

    public function test_per_callable_priority(): void
    {
        $invokator = $this->getInvokator();
        $invokator->pipeline('p')
            ->add(fn ($x): string => $x . '-low', 0)
            ->add(fn ($x): string => $x . '-high', 10);

        $this->assertSame('start-high-low', $invokator->pipeline('p')->run('start'));
    }

    public function test_filter_threads_the_value(): void
    {
        $invokator = $this->getInvokator();
        $invokator->filter('up')->add(fn ($v): string => strtoupper((string) $v));

        $this->assertSame('HELLO', $invokator->filter('up')->run('hello'));
    }

    public function test_action_runs_for_side_effects_and_returns_null(): void
    {
        $invokator = $this->getInvokator();
        $log = [];
        $invokator->action('log')->add(function ($x) use (&$log): void {
            $log[] = $x;
        });

        $this->assertNull($invokator->action('log')->run('hi'));
        $this->assertSame(['hi'], $log);
    }

    public function test_middleware_wraps_with_next(): void
    {
        $invokator = $this->getInvokator();
        $invokator->middleware('m')
            ->add(fn ($name, $next = null): string => strtoupper((string) $next($name)))
            ->add(fn ($name, $next = null): string => 'Hello ' . $next($name))
            ->add(fn ($name, $next = null) => $name);

        $this->assertSame('HELLO WORLD', $invokator->middleware('m')->run('world'));
    }

    public function test_event_subscribe_and_dispatch(): void
    {
        $invokator = $this->getInvokator();
        $seen = [];
        $invokator->event(EventWithoutName::class)->add(function (object $event) use (&$seen): void {
            $seen[] = $event::class;
        });

        $returned = $invokator->dispatch(new EventWithoutName());

        $this->assertInstanceOf(EventWithoutName::class, $returned);
        $this->assertSame([EventWithoutName::class], $seen);
    }

    public function test_event_run_is_an_alias_for_dispatch(): void
    {
        $invokator = $this->getInvokator();
        $seen = [];
        $invokator->event(EventWithoutName::class, function (object $event) use (&$seen): void {
            $seen[] = 'seen';
        });

        $invokator->event(EventWithoutName::class)->run(new EventWithoutName());

        $this->assertSame(['seen'], $seen);
    }

    public function test_stoppable_event_halts_propagation(): void
    {
        $invokator = $this->getInvokator();
        $order = [];
        $invokator->event(StoppableEvent::class)->add(function () use (&$order): void {
            $order[] = 1;
        });
        $invokator->event(StoppableEvent::class)->add(function (StoppableEvent $event) use (&$order): void {
            $order[] = 2;
            $event->stopPropagation();
        });
        $invokator->event(StoppableEvent::class)->add(function () use (&$order): void {
            $order[] = 3;
        });

        $invokator->dispatch(new StoppableEvent());

        $this->assertSame([1, 2], $order);
    }

    public function test_command_middleware_and_auto_discovered_handler(): void
    {
        $this->getContainer()->register(SampleHandler::class, new SampleHandler());
        $invokator = $this->getInvokator();
        $invokator->command(SampleCommand::class)
            ->add(fn ($command, $next = null): int|float => 2 * $next($command));

        $this->assertEquals(2 * (2 + 5), $invokator->handle(new SampleCommand(2, 5)));
    }

    public function test_command_run_with_explicit_handler(): void
    {
        $invokator = $this->getInvokator();
        $result = $invokator->command(SampleCommand::class)
            ->handledBy(fn (SampleCommand $command): int => $command->first * $command->second)
            ->run(new SampleCommand(2, 5));

        $this->assertEquals(10, $result);
    }
}
