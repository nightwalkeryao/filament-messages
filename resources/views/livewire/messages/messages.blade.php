@php
    use Raseldev99\FilamentMessages\Enums\MediaCollectionType;
@endphp

@props(['selectedConversation'])

<div @class([
    'flex-1 flex flex-col h-full bg-white dark:bg-gray-900 shadow-sm rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden min-h-[500px]',
])>
    @if ($selectedConversation)
        <!-- Chat Header -->
        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/30 dark:bg-white/5 flex items-center justify-between sticky top-0 z-10 backdrop-blur-sm">
            <div class="flex items-center gap-4 overflow-hidden">
                @php
                    $avatar = "https://ui-avatars.com/api/?name=" . urlencode($selectedConversation->inbox_title);
                    $alt = urlencode($selectedConversation->inbox_title);
                @endphp
                <x-filament::avatar
                    src="{{ $avatar }}"
                    alt="{{ $alt }}" 
                    size="lg" 
                    class="ring-2 ring-primary-500/10 shadow-sm"
                />
                <div class="overflow-hidden">
                    <h3 class="text-base font-bold text-gray-900 dark:text-white truncate leading-tight">
                        {{ $selectedConversation->inbox_title }}
                    </h3>
                    @if ($selectedConversation->title)
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate opacity-80">
                            {{ $selectedConversation->other_users->pluck('name')->implode(', ') }}
                        </p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <!-- Potential Action Buttons could go here -->
            </div>
        </div>

        <!-- Chat Messages Area -->
        <div 
            wire:poll.visible.{{ $pollInterval }}="pollMessages" 
            id="chatContainer" 
            class="flex-1 flex flex-col-reverse p-6 overflow-y-auto custom-scrollbar gap-4"
        >
            @foreach ($conversationMessages as $index => $message)
                @php
                    $is_sent_by_me = $message->user_id === auth()->id();
                    $prev_message = $conversationMessages[$index + 1] ?? null;
                    $is_same_sender = $prev_message && $prev_message->user_id === $message->user_id;
                @endphp

                <div @class([
                    'flex flex-col gap-1 max-w-[85%] md:max-w-[70%]',
                    'self-end items-end' => $is_sent_by_me,
                    'self-start items-start' => !$is_sent_by_me,
                ]) wire:key="msg-{{ $message->id }}">
                    
                    @if (!$is_sent_by_me && !$is_same_sender)
                        <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 ml-1 mb-1 uppercase tracking-wider">
                            {{ $message->sender->name }}
                        </span>
                    @endif

                    <div @class([
                        'group relative px-4 py-2.5 rounded-2xl text-sm shadow-sm transition-all duration-200',
                        'bg-primary-600 text-white rounded-tr-none' => $is_sent_by_me,
                        'bg-gray-100 dark:bg-white/10 text-gray-900 dark:text-white rounded-tl-none' => !$is_sent_by_me,
                    ])>
                        @if ($message->message)
                            <div class="leading-relaxed whitespace-pre-wrap break-words">{!! nl2br(e($message->message)) !!}</div>
                        @endif

                        @if ($message->getMedia(MediaCollectionType::FILAMENT_MESSAGES->value)->isNotEmpty())
                            <div class="mt-2 space-y-2">
                                @foreach ($message->getMedia(MediaCollectionType::FILAMENT_MESSAGES->value) as $media)
                                    <button 
                                        type="button"
                                        wire:click="downloadAttachment('{{ $media->getPath() }}', '{{ $media->file_name }}')"
                                        @class([
                                            'flex items-center gap-3 p-2 rounded-xl text-xs w-full transition-all duration-200 border',
                                            'bg-white/10 border-white/20 hover:bg-white/20 text-white' => $is_sent_by_me,
                                            'bg-white dark:bg-gray-800 border-gray-200 dark:border-white/5 hover:border-primary-500 text-gray-700 dark:text-gray-300' => !$is_sent_by_me,
                                        ])
                                    >
                                        <div @class([
                                            'p-1.5 rounded-lg',
                                            'bg-white/20' => $is_sent_by_me,
                                            'bg-primary-50 dark:bg-primary-500/10 text-primary-600' => !$is_sent_by_me,
                                        ])>
                                            @php
                                                $icon = 'heroicon-o-document';
                                                if($this->validateImage($media->getFullUrl())) $icon = 'heroicon-o-photo';
                                                elseif($this->validateVideo($media->getFullUrl())) $icon = 'heroicon-o-video-camera';
                                                elseif($this->validateAudio($media->getFullUrl())) $icon = 'heroicon-o-speaker-wave';
                                            @endphp
                                            <x-filament::icon icon="{{ $icon }}" class="w-4 h-4" />
                                        </div>
                                        <span class="truncate font-medium">{{ $media->file_name }}</span>
                                        <x-filament::icon icon="heroicon-o-arrow-down-tray" class="w-3 h-3 ml-auto opacity-50" />
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center gap-2 px-1">
                        <time class="text-[10px] text-gray-400 dark:text-gray-500 italic">
                            @php
                                $createdAt = \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filament-messages.timezone', 'app.timezone'));
                                $dateStr = $createdAt->isToday() ? $createdAt->format('g:i A') : $createdAt->format('M d, g:i A');
                            @endphp
                            {{ $dateStr }}
                        </time>
                        @if ($is_sent_by_me)
                            <x-filament::icon icon="heroicon-o-check-circle" class="w-3 h-3 text-primary-500 opacity-60" />
                        @endif
                    </div>
                </div>

                @php
                    $nextMessage = $conversationMessages[$index + 1] ?? null;
                    $nextDate = $nextMessage ? \Carbon\Carbon::parse($nextMessage->created_at)->setTimezone(config('filament-messages.timezone', 'app.timezone'))->format('Y-m-d') : null;
                    $currDate = \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filament-messages.timezone', 'app.timezone'))->format('Y-m-d');
                @endphp
                
                @if ($currDate !== $nextDate)
                    <div class="flex items-center justify-center my-6 relative">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-100 dark:border-white/5"></div>
                        </div>
                        <span class="relative px-3 py-1 text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-600 bg-white dark:bg-gray-900 rounded-full border border-gray-100 dark:border-white/5 shadow-sm">
                            {{ \Carbon\Carbon::parse($message->created_at)->setTimezone(config('filament-messages.timezone', 'app.timezone'))->translatedFormat('F j, Y') }}
                        </span>
                    </div>
                @endif
            @endforeach

            @if ($this->paginator->hasMorePages())
                <div x-intersect="$wire.loadMessages" class="py-4">
                    <div class="flex justify-center">
                        <x-filament::loading-indicator class="w-6 h-6 text-primary-500 animate-pulse" />
                    </div>
                </div>
            @endif
        </div>

        <!-- Chat Input -->
        <div class="p-4 border-t border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-white/5">
            <form wire:submit="sendMessage" class="flex items-end gap-3 max-w-5xl mx-auto">
                <div class="flex-1 bg-white dark:bg-gray-800 rounded-2xl shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 focus-within:ring-primary-500 transition-all duration-200">
                    {{ $this->form }}
                </div>
                <div class="mb-1">
                    <x-filament::button 
                        type="submit" 
                        size="lg" 
                        icon="heroicon-o-paper-airplane" 
                        :disabled="$this->validateMessage()"
                        class="rounded-full shadow-lg hover:translate-y-[-1px] transition-all duration-200"
                    >
                        {{ __('Send') }}
                    </x-filament::button>
                </div>
            </form>
            <x-filament-actions::modals />
        </div>
    @else
        <!-- No Selected Conversation State -->
        <div class="flex-1 flex flex-col items-center justify-center p-12 text-center animate-in zoom-in duration-500">
            <div class="relative mb-6">
                <div class="absolute -inset-4 bg-primary-500/10 blur-2xl rounded-full animate-pulse"></div>
                <div class="relative p-8 bg-gray-50 dark:bg-white/5 rounded-3xl ring-1 ring-gray-950/5 dark:ring-white/10 shadow-xl overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-700"></div>
                    <x-filament::icon icon="heroicon-o-chat-bubble-left-right" class="w-20 h-20 text-gray-300 dark:text-gray-600 relative z-10" />
                </div>
            </div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 tracking-tight">
                {{ __('Your Conversations') }}
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 max-w-sm leading-relaxed mb-8">
                {{ __('Select a thread from the list on the left or create a new one to start messaging your team members.') }}
            </p>
        </div>
    @endif
</div>

@script
<script>
    $wire.on('chat-box-scroll-to-bottom', () => {
        const container = document.getElementById('chatContainer');
        if (!container) return;

        container.scrollTo({
            top: container.scrollHeight,
            behavior: 'smooth',
        });

        setTimeout(() => {
            container.scrollTop = container.scrollHeight;
        }, 400);
    });
</script>
@endscript
