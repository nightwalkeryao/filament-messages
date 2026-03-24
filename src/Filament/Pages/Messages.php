<?php

namespace Raseldev99\FilamentMessages\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Raseldev99\FilamentMessages\Models\Inbox;

class Messages extends Page
{
    protected static string $view = 'filament-messages::filament.pages.messages';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static null|string $slug = 'messages/{id?}';

    protected null|string $heading = '';

    protected Width|string|null $maxContentWidth = Width::Full;

    public null|Inbox $selectedConversation;

    public function mount(null|int $id = null): void
    {
        if ($id) {
            $this->selectedConversation = Inbox::findOrFail($id);
        }
    }
}
