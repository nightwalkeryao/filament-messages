<?php

namespace Raseldev99\FilamentMessages;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Raseldev99\FilamentMessages\Filament\Pages\Messages;

class FilamentMessagesPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-messages';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->pages([
                Messages::class,
            ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
