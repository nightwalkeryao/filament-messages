<x-filament-panels::page @class(['fi-resource-messages-page'])>
    <div @class([
        'grid grid-cols-1 lg:grid-cols-12 gap-4 h-[calc(100vh-10rem)] min-h-[600px]',
    ])>
        <!-- Inbox Sidebar -->
        <div @class([
            'lg:col-span-4 xl:col-span-3 h-full flex flex-col overflow-hidden',
        ])>
            <livewire:fm-inbox :selectedConversation="$selectedConversation" />
        </div>

        <!-- Chat Area -->
        <div @class([
            'lg:col-span-8 xl:col-span-9 h-full flex flex-col overflow-hidden',
        ])>
            <livewire:fm-messages :selectedConversation="$selectedConversation" />
        </div>
    </div>
    <livewire:fm-search />
</x-filament-panels::page>
