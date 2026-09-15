<?php

namespace Ugarit\Jetstream\Tests;

use Heritage\Foundation\Testing\LazilyRefreshDatabase;
use Ugarit\Fortify\FortifyServiceProvider;
use Ugarit\Jetstream\Features;
use Ugarit\Jetstream\JetstreamServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\Attributes\WithConfig;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase;

#[WithConfig('database.default', 'testing')]
abstract class OrchestraTestCase extends TestCase
{
    use LazilyRefreshDatabase, WithWorkbench;

    protected function defineHasTeamEnvironment($app)
    {
        $features = $app->config->get('jetstream.features', []);

        $features[] = Features::teams(['invitations' => true]);

        $app->config->set('jetstream.features', $features);
    }
}
