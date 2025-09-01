<div class="flex h-screen bg-gray-100">
    <!-- Conversations Sidebar -->
    <div class="w-1/3 bg-white border-r border-gray-300 flex flex-col">
        <!-- Header -->
        <div class="px-4 py-4 bg-primary-600 text-white">
            <h2 class="text-lg font-semibold">Messages</h2>
            
            <!-- Search -->
            <div class="mt-3">
                <input type="text" 
                       wire:model.live="searchTerm"
                       placeholder="Search conversations..."
                       class="w-full px-3 py-2 text-gray-900 bg-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-primary-300">
            </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto">
            @forelse($conversations as $conversation)
                <div wire:click="selectConversation({{ $conversation->id }})"
                     class="flex items-center px-4 py-3 border-b border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors duration-200 {{ $selectedConversation && $selectedConversation->id === $conversation->id ? 'bg-blue-50 border-l-4 border-l-primary-600' : '' }}">
                    
                    <!-- Avatar -->
                    <div class="flex-shrink-0 mr-3">
                        @php
                            $participant = $conversation->customer ?? $conversation->merchant ?? $conversation->admin;
                        @endphp
                        
                        @if($participant && $participant->avatar)
                            <img src="{{ asset('storage/' . $participant->avatar) }}" 
                                 alt="{{ $participant->name }}" 
                                 class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                        
                        <!-- Online indicator -->
                        @if(in_array($participant?->id, $onlineUsers))
                            <div class="w-3 h-3 bg-green-500 rounded-full border-2 border-white -mt-8 ml-7"></div>
                        @endif
                    </div>

                    <!-- Conversation Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                {{ $participant->name ?? 'Unknown' }}
                            </h3>
                            @if($conversation->lastMessage)
                                <span class="text-xs text-gray-500">
                                    {{ $conversation->lastMessage->created_at->diffForHumans() }}
                                </span>
                            @endif
                        </div>
                        
                        @if($conversation->lastMessage)
                            <p class="text-sm text-gray-600 truncate mt-1">
                                {{ $conversation->lastMessage->content }}
                            </p>
                        @endif
                        
                        <!-- Unread indicator -->
                        @php
                            $unreadCount = $conversation->messages()
                                ->where('sender_id', '!=', auth()->id())
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        
                        @if($unreadCount > 0)
                            <div class="flex justify-end mt-1">
                                <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                    {{ $unreadCount }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <p class="text-lg font-medium mb-2">No conversations yet</p>
                    <p class="text-sm">Start a new conversation to get connected!</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        @if($selectedConversation)
            <!-- Chat Header -->
            <div class="px-6 py-4 bg-white border-b border-gray-300 flex items-center justify-between">
                <div class="flex items-center">
                    @php
                        $participant = $selectedConversation->customer ?? $selectedConversation->merchant ?? $selectedConversation->admin;
                    @endphp
                    
                    @if($participant && $participant->avatar)
                        <img src="{{ asset('storage/' . $participant->avatar) }}" 
                             alt="{{ $participant->name }}" 
                             class="w-10 h-10 rounded-full object-cover mr-3">
                    @else
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    @endif
                    
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">{{ $participant->name ?? 'Unknown' }}</h3>
                        <p class="text-sm text-gray-500">
                            @if(in_array($participant?->id, $onlineUsers))
                                <span class="text-green-600">● Online</span>
                            @else
                                Last seen {{ $participant?->updated_at?->diffForHumans() ?? 'unknown' }}
                            @endif
                        </p>
                    </div>
                </div>
                
                <!-- Chat Actions -->
                <div class="flex items-center space-x-2">
                    <button wire:click="archiveConversation({{ $selectedConversation->id }})"
                            class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8l4-4 4 4m0 0L9 12l-4-4m4 4v12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto px-6 py-4 space-y-4" id="messages-container">
                @foreach($messages as $message)
                    <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg {{ $message->sender_id === auth()->id() ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                            <!-- Message Content -->
                            @if($message->type === 'text')
                                <p class="text-sm">{{ $message->content }}</p>
                            @elseif($message->type === 'file')
                                <div class="space-y-2">
                                    @if($message->content)
                                        <p class="text-sm">{{ $message->content }}</p>
                                    @endif
                                    
                                    @if($message->attachments)
                                        @foreach($message->attachments as $attachment)
                                            <div class="flex items-center space-x-2 p-2 bg-white bg-opacity-20 rounded">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" 
                                                   target="_blank" 
                                                   class="text-sm underline">
                                                    {{ $attachment['name'] }}
                                                </a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Message Meta -->
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs opacity-70">
                                    {{ $message->created_at->format('H:i') }}
                                </span>
                                
                                @if($message->sender_id === auth()->id())
                                    <div class="flex items-center space-x-1">
                                        @if($message->is_read)
                                            <svg class="w-3 h-3 opacity-70" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                        
                                        <button wire:click="deleteMessage({{ $message->id }})"
                                                class="text-xs opacity-50 hover:opacity-70">
                                            ×
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Message Input -->
            <div class="px-6 py-4 bg-white border-t border-gray-300">
                <form wire:submit="sendMessage" class="flex items-end space-x-3">
                    <!-- File Upload -->
                    <div>
                        <input type="file" 
                               wire:model="attachments" 
                               multiple 
                               id="file-upload" 
                               class="hidden">
                        <label for="file-upload" 
                               class="flex items-center justify-center w-10 h-10 text-gray-500 hover:text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg cursor-pointer transition-colors duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </label>
                    </div>

                    <!-- Message Input -->
                    <div class="flex-1">
                        <textarea wire:model="newMessage"
                                  placeholder="Type a message..."
                                  rows="1"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent resize-none"
                                  style="min-height: 40px; max-height: 120px;"
                                  onkeydown="if(event.key==='Enter' && !event.shiftKey){event.preventDefault(); @this.sendMessage()}"></textarea>
                        
                        <!-- Attachment Preview -->
                        @if(!empty($attachments))
                            <div class="mt-2 space-y-1">
                                @foreach($attachments as $index => $attachment)
                                    <div class="flex items-center justify-between p-2 bg-gray-100 rounded text-sm">
                                        <span>{{ $attachment->getClientOriginalName() }}</span>
                                        <button type="button" 
                                                wire:click="$set('attachments.{{ $index }}', null)"
                                                class="text-red-500 hover:text-red-700">
                                            ×
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <!-- Send Button -->
                    <button type="submit"
                            class="flex items-center justify-center w-10 h-10 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors duration-200"
                            :disabled="!newMessage.trim() && attachments.length === 0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
        @else
            <!-- No Conversation Selected -->
            <div class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center">
                    <svg class="w-24 h-24 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-900 mb-2">Select a conversation</h3>
                    <p class="text-gray-500">Choose a conversation from the sidebar to start messaging</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Loading States -->
    <div wire:loading wire:target="sendMessage" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-3">
            <svg class="animate-spin w-5 h-5 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-gray-900">Sending message...</span>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll to bottom when new messages arrive
    document.addEventListener('livewire:navigated', function () {
        const container = document.getElementById('messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    });

    // Listen for new messages and scroll to bottom
    window.addEventListener('message-sent', function (event) {
        setTimeout(() => {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }, 100);
    });
</script>
@endpush
