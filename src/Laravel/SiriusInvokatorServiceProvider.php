<?php

declare(strict_types=1);

namespace Sirius\Invokator\Laravel;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Sirius\Invokator\Callables\CommandBus;
use Sirius\Invokator\Event\Dispatcher;
use Sirius\Invokator\Invokator;
use Sirius\Invokator\Invoker;

class SiriusInvokatorServiceProvider extends ServiceProvider
{
    #[\Override]
    public function register(): void
    {
        // Laravel's container implements Psr\Container\ContainerInterface, so it can be
        // injected straight into the Invoker as its PSR container.
        $this->app->singleton(Invoker::class, fn ($app): Invoker => new Invoker($app));

        $this->app->singleton(Invokator::class, fn ($app): Invokator => new Invokator($app->make(Invoker::class)));
        $this->app->alias(Invokator::class, 'invokator');

        // Expose the PSR-14 dispatcher and the command bus owned by the Invokator so they
        // remain injectable on their own.
        $this->app->singleton(Dispatcher::class, fn ($app): Dispatcher => $app->make(Invokator::class)->dispatcher());
        $this->app->singleton(CommandBus::class, fn ($app): CommandBus => $app->make(Invokator::class)->commandBus());

        require_once __DIR__ . '/helpers.php';
    }

    public function boot(): void
    {
        Blade::directive('do_action', static fn (string $expression): string => "<?php do_action({$expression}); ?>");
    }
}
