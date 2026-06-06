<?php

declare(strict_types=1);

namespace Sirius\Invokator\Laravel;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Sirius\Invokator\Event\Dispatcher;
use Sirius\Invokator\Event\ListenerProvider;
use Sirius\Invokator\Invoker;
use Sirius\Invokator\Processors\ActionsProcessor;
use Sirius\Invokator\Processors\FiltersProcessor;
use Sirius\Invokator\Processors\MiddlewareProcessor;
use Sirius\Invokator\Processors\PipelineProcessor;

class SiriusInvokatorServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        // Laravel's container implements Psr\Container\ContainerInterface, so it can be
        // injected straight into the Invoker as its PSR container.
        $this->app->singleton(Invoker::class, fn ($app): Invoker => new Invoker($app));

        foreach ([PipelineProcessor::class, ActionsProcessor::class, FiltersProcessor::class, MiddlewareProcessor::class] as $processor) {
            $this->app->singleton($processor, fn ($app): PipelineProcessor|\Sirius\Invokator\Processors\ActionsProcessor|\Sirius\Invokator\Processors\FiltersProcessor|\Sirius\Invokator\Processors\MiddlewareProcessor => new $processor($app->make(Invoker::class)));
        }

        $this->app->singleton(ListenerProvider::class);
        $this->app->singleton(Dispatcher::class, fn ($app): Dispatcher => new Dispatcher(
            $app->make(ListenerProvider::class),
            $app->make(Invoker::class),
        ));

        $this->app->singleton(InvokatorManager::class, fn ($app): InvokatorManager => new InvokatorManager(
            $app->make(PipelineProcessor::class),
            $app->make(ActionsProcessor::class),
            $app->make(FiltersProcessor::class),
            $app->make(MiddlewareProcessor::class),
            $app->make(Dispatcher::class),
        ));
        $this->app->alias(InvokatorManager::class, 'invokator');

        require_once __DIR__ . '/helpers.php';
    }

    public function boot(): void
    {
        Blade::directive('do_action', static fn (string $expression): string => "<?php do_action({$expression}); ?>");
    }
}
