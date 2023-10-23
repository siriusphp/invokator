<?php

namespace Sirius\Invokator\Processors;

use Sirius\Invokator\CommandBus\SampleCommand;
use Sirius\Invokator\CommandBus\SampleHandler;
use Sirius\Invokator\TestCase;

class CommandBusTest extends TestCase
{
    public function test_automatic_handler()
    {
        $this->getContainer()->register(SampleHandler::class, new SampleHandler());
        $bus = new CommandBus($this->getInvoker());
        $bus->addMiddleware(SampleCommand::class, function ($name, $next = null) {
            return 2 * $next($name);
        });

        $result = $bus->handle(new SampleCommand(2, 5));

        $this->assertEquals(2 * (2 + 5), $result);
    }

    public function test_custom_handler()
    {
        $bus = new CommandBus($this->getInvoker());
        $bus->addMiddleware(SampleCommand::class, function ($name, $next = null) {
            return 2 * $next($name);
        });
        $bus->register(SampleCommand::class, function (SampleCommand $command) {
            return $command->first * $command->second;
        });

        $result = $bus->handle(new SampleCommand(2, 5));

        $this->assertEquals(2 * 2 * 5, $result);
    }

    public function test_no_middleware()
    {
        $this->getContainer()->register(SampleHandler::class, new SampleHandler());
        $bus = new CommandBus($this->getInvoker());

        $result = $bus->handle(new SampleCommand(2, 5));

        $this->assertEquals(2 + 5, $result);
    }
}
