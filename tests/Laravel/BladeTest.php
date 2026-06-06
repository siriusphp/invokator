<?php

declare(strict_types=1);

namespace Sirius\Invokator\Tests\Laravel;

use Illuminate\Support\Facades\Blade;
use Sirius\Invokator\Laravel\Facades\Invokator;

class BladeTest extends TestCase
{
    public function test_do_action_blade_directive_runs_the_action(): void
    {
        $log = [];
        Invokator::action('analytics')->add(function ($user) use (&$log): void {
            $log[] = $user;
        });

        // Blade only recognises @directive when it isn't glued to a preceding word char
        // (here it follows `>`). The action returns null, so the directive emits nothing.
        $html = Blade::render("<head>@do_action('analytics', \$user)</head>", ['user' => 'sam']);

        $this->assertSame('<head></head>', $html);
        $this->assertSame(['sam'], $log);
    }

    public function test_do_filter_function_in_blade_echo(): void
    {
        Invokator::filter('shout')->add(fn ($v): string => strtoupper((string) $v) . '!');

        $html = Blade::render("{{ do_filter('shout', \$title) }}", ['title' => 'hey']);

        $this->assertSame('HEY!', $html);
    }
}
