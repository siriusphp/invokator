<?php

namespace Sirius\Invokator\Modifiers;

use Sirius\Invokator\Processors\SimpleCallablesProcessor;
use Sirius\Invokator\TestCase;
use Sirius\Invokator\Utilities\DependencyClass;
use Sirius\Invokator\Utilities\DependentClass;
use function Sirius\Invokator\ref;
use function Sirius\Invokator\arg;
use function Sirius\Invokator\resolve;
use function Sirius\Invokator\result_of;
use function Sirius\Invokator\with_arguments;
use function Sirius\Invokator\wrap;


class ResolveArgumentsTest extends TestCase
{
    static public $results = [];

    protected function setUp(): void
    {
        parent::setUp();
        static::$results = [];
        $this->getContainer()->register(DependentClass::class, new DependentClass());
        $this->getContainer()->register(DependencyClass::class, new DependencyClass());
    }

    public function test_resolve_arguments()
    {
        $this->getContainer()->register('test_param', 'C');
        $processor = new SimpleCallablesProcessor($this->getInvoker());
        $processor->add('test', wrap(resolve(DependentClass::class . '@multiply', ['firstNumber' => 5, 'secondNumber' => arg(0)]), function($next){
            static::$results[] = $next();
        }));

        $processor->process('test', 1);

        $this->assertSame([
            5 * (1 + 5),
        ], static::$results);
    }
}
