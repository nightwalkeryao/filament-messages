<?php

namespace Raseldev99\FilamentMessages\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Raseldev99\FilamentMessages\Support\FilamentCompat;

class FilamentCompatTest extends TestCase
{
    public function test_register_icons_is_callable_and_safe()
    {
        // Should not throw even if Filament is not installed in the test environment
        FilamentCompat::registerIcons([]);
        $this->assertTrue(true);
    }

    public function test_register_assets_is_callable_and_safe()
    {
        FilamentCompat::registerAssets([]);
        $this->assertTrue(true);
    }

    public function test_register_livewire_component_is_callable_and_safe()
    {
        FilamentCompat::registerLivewireComponent('test-foo', \stdClass::class);
        $this->assertTrue(true);
    }
}
