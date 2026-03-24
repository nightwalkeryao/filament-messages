<?php

namespace Raseldev99\FilamentMessages;

use Raseldev99\FilamentMessages\Commands\FilamentMessagesCommand;
use Raseldev99\FilamentMessages\Livewire\Messages\Inbox;
use Raseldev99\FilamentMessages\Livewire\Messages\Messages;
use Raseldev99\FilamentMessages\Livewire\Messages\Search;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMessagesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-messages')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigrations($this->getMigrations())
            ->hasCommand(FilamentMessagesCommand::class);
    }

    public function packageBooted(): void
    {
        parent::packageBooted();

        // Livewire
        Livewire::component('fm-inbox', Inbox::class);
        Livewire::component('fm-messages', Messages::class);
        Livewire::component('fm-search', Search::class);
    }

    protected function getMigrations(): array
    {
        return [
            'create_fm_inboxes_table',
            'create_fm_messages_table',
        ];
    }
}

