@php
    use Raseldev99\FilamentMessages\Filament\Pages\Messages;
    use Raseldev99\FilamentMessages\Enums\MediaCollectionType;
@endphp

@props(['selectedConversation'])

<div wire:poll.visible.{{ $pollInterval }}="loadConversations" 
    class="flex-1 flex flex-col h-full bg-white dark:bg-gray-900 shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
    
    <!-- Sidebar Header: Search & Create -->
    <div class="px-5 py-6 border-b border-gray-100 dark:border-white/5 space-y-4 shadow-sm bg-gray-50/30 dark:bg-white/5">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white leading-none tracking-tight">
                {{__('Inbox')}}
                @if ($this->unreadCount() > 0)
                    <span class="inline-flex items-center justify-center min-w-5 h-5 px-1 ml-2 text-[10px] font-medium text-white bg-primary-600 rounded-full ring-2 ring-white dark:ring-gray-900">
                        {{ $this->unreadCount() }}
                    </span>
                @endif
            </h2>
            <div class="flex-shrink-0">
                {{ $this->createConversationAction }}
            </div>
        </div>

        <div class="relative group">
            <x-filament::input.wrapper suffix-icon="heroicon-o-magnifying-glass" class="group-hover:ring-primary-500 transition-all duration-300">
                <x-filament::input 
                    type="text" 
                    placeholder="{{__('Search conversations...')}}" 
                    readonly
                    class="cursor-pointer"
                    x-on:click="$dispatch('open-modal', { id: 'search-conversation' })"
                />
            </x-filament::input.wrapper>
        </div>
    </div>

    <!-- Conversation List -->
    <div class="flex-1 overflow-y-auto custom-scrollbar p-3">
        @if ($this->conversations->count() > 0)
            <div class="flex flex-col gap-1">
                @foreach ($this->conversations as $conversation)
                    @php
                        $is_selected = $conversation->id == $selectedConversation?->id;
                        $has_unread = !in_array(auth()->id(), $conversation->latestMessage()->read_by);
                        $latest_message = $conversation->latestMessage();
                    @endphp

                    <a wire:key="conv-{{ $conversation->id }}" 
                        wire:navigate
                        href="{{ Messages::getUrl(tenant: filament()->getTenant()) . '/' . $conversation->id }}"
                        @class([
                            'group flex items-center gap-3 p-3 rounded-xl transition-all duration-200 border border-transparent',
                            'bg-gray-50 dark:bg-white/5 border-gray-100 dark:border-white/10 shadow-sm' => $is_selected,
                            'hover:bg-gray-50 dark:hover:bg-white/5' => !$is_selected,
                            'border-l-4 border-l-primary-500' => $has_unread && !$is_selected,
                        ])>
                        
                        <div class="relative flex-shrink-0">
                            @php
                                $avatar = "https://ui-avatars.com/api/?name=" . urlencode($conversation->inbox_title);
                                $alt = urlencode($conversation->inbox_title);
                            @endphp
                            <x-filament::avatar
                                src="{{ $avatar }}"
                                alt="{{ $alt }}" 
                                size="lg" 
                                class="ring-2 ring-transparent group-hover:ring-primary-500 transition-all duration-300"
                            />
                            @if($has_unread)
                                <span class="absolute top-0 right-0 w-3 h-3 bg-primary-500 rounded-full border-2 border-white dark:border-gray-900 shadow-sm"></span>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 @class([
                                    'text-sm truncate font-semibold',
                                    'text-gray-900 dark:text-white' => $has_unread || $is_selected,
                                    'text-gray-600 dark:text-gray-400' => !$has_unread && !$is_selected,
                                ])>
                                    {{ $conversation->inbox_title }}
                                </h3>
                                <time class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0 ml-2">
                                    {{ \Carbon\Carbon::parse($conversation->updated_at)->setTimezone(config('filament-messages.timezone', 'app.timezone'))->shortAbsoluteDiffForHumans() }}
                                </time>
                            </div>
                            
                            <p @class([
                                'text-xs truncate transition-colors duration-200',
                                'text-gray-900 dark:text-gray-200 font-medium' => $has_unread,
                                'text-gray-500 dark:text-gray-400' => !$has_unread,
                            ])>
                                <span class="font-bold opacity-60">
                                    {{ $latest_message->user_id == auth()->id() ? __('You:') : $latest_message->sender->name . ':' }}
                                </span>
                                @if ($latest_message->getMedia(MediaCollectionType::FILAMENT_MESSAGES->value)->isNotEmpty())
                                    <span class="inline-flex items-center gap-1 italic text-primary-600">
                                        <x-filament::icon icon="heroicon-o-paper-clip" class="w-3 h-3" />
                                        {{ $latest_message->getMedia(MediaCollectionType::FILAMENT_MESSAGES->value)->count() > 1 ? __('Attachments') : __('Attachment') }}
                                    </span>
                                @else
                                    {{ $latest_message->message }}
                                @endif
                            </p>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="flex flex-col items-center justify-center p-8 py-16 text-center animate-in fade-in duration-700">
                <div class="relative mb-4">
                    <div class="absolute -inset-1 bg-primary-500/20 blur rounded-full animate-pulse"></div>
                    <div class="relative p-4 bg-gray-100 dark:bg-gray-800 rounded-full ring-1 ring-gray-950/5 dark:ring-white/10 shadow-inner">
                        <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="w-10 h-10 text-gray-400 dark:text-gray-500" />
                    </div>
                </div>
                <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{__('No messages yet')}}</h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 max-w-[200px] leading-relaxed">
                    {{__('Start a new conversation to connect with your team.')}}
                </p>
            </div>
        @endif
    </div>
</div>
