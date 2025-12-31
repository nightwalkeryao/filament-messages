<?php

namespace Raseldev99\FilamentMessages\Tests\Feature;

use PHPUnit\Framework\TestCase;

class CompatibilityTest extends TestCase
{
    public function test_service_provider_is_autoloadable(): void
    {
        $this->assertTrue(class_exists(\Raseldev99\FilamentMessages\FilamentMessagesServiceProvider::class));
    }
}
