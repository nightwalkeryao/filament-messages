@php
    use Raseldev99\FilamentMessages\Filament\Pages\Messages;
@endphp
<x-filament::modal width="xl" id="search-conversation">
    <x-slot name="heading">
        <div class="flex items-center gap-2">
            <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-5 h-5 text-primary-500" />
            <span>{{__('Search Messages')}}</span>
        </div>
    </x-slot>

    <div class="space-y-4">
        <x-filament::input.wrapper suffix-icon="heroicon-o-magnifying-glass" class="ring-primary-500/20">
            <x-filament::input 
                type="search" 
                placeholder="{{__('Search messages...')}}" 
                wire:model.live.debounce.500ms="search"
                autofocus
            />
        </x-filament::input.wrapper>

        <div class="max-h-[60vh] overflow-y-auto custom-scrollbar -mx-6 px-6">
            @if(count($messages) > 0)
                <ul class="divide-y divide-gray-100 dark:divide-white/5 border-t border-gray-100 dark:border-white/5">
                    @foreach($messages as $message)
                        <li wire:key="search-msg-{{ $message->id }}">
                            <a wire:navigate 
                                href="{{ Messages::getUrl(tenant: filament()->getTenant()) . '/' . $message->inbox->id }}"
                                class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-200"
                            >
                                @php
                                    $avatar = "https://ui-avatars.com/api/?name=" . urlencode($message->inbox->inbox_title);
                                    $alt = urlencode($message->inbox->inbox_title);
                                @endphp
                                <div class="flex-shrink-0">
                                    <x-filament::avatar
                                        src="{{ $avatar }}"
                                        alt="{{ $alt }}" 
                                        size="lg" 
                                        class="ring-1 ring-gray-950/5"
                                    />
                                </div>
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between gap-2 mb-0.5">
                                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate">
                                            {{ $message->inbox->inbox_title }}
                                        </p>
                                        <time class="text-[10px] text-gray-400 dark:text-gray-500 flex-shrink-0">
                                            {{ \Carbon\Carbon::parse($message->updated_at)->setTimezone(config('filament-messages.timezone', 'app.timezone'))->format('M d, Y') }}
                                        </time>
                                    </div>
                                    <p class="text-xs text-gray-600 dark:text-gray-400 truncate leading-relaxed">
                                        <span class="font-bold opacity-70">
                                            {{ $message->user_id == auth()->id() ? __('You:') : $message->sender->name . ':' }}
                                        </span>
                                        {{ $message->message ?: __('Attachment') }}
                                    </p>
                                </div>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @elseif(!empty($search))
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="p-4 bg-gray-50 dark:bg-white/5 rounded-full mb-4">
                        <x-filament::icon icon="heroicon-o-magnifying-glass" class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{__('No results found')}}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-[200px]">
                        {{__('Try adjusting your search criteria to find what you are looking for.')}}
                    </p>
                </div>
            @else
                <div class="py-12 flex flex-col items-center justify-center text-center text-gray-400 dark:text-gray-600 italic">
                    <p class="text-xs">{{ __('Type to start searching your message history...') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-filament::modal>
