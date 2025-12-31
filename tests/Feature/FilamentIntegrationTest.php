<?php

namespace Raseldev99\FilamentMessages\Tests\Feature;

use Orchestra\Testbench\TestCase;
use Raseldev99\FilamentMessages\FilamentMessagesServiceProvider;

class FilamentIntegrationTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            FilamentMessagesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Minimal Filament config to avoid boot issues
        $app['config']->set('filament.path', '/admin');
    }

    public function test_package_registers_without_errors()
    {
        // If register/boot throws, the test will fail
        $this->app->register(FilamentMessagesServiceProvider::class);

        $this->assertTrue(true);
    }
}
