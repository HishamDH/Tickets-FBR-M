@extends('layouts.app')

@section('title', 'المحادثات - Chat')
@section('description', 'واجهة المحادثات والرسائل الفورية')

@push('styles')
<style>
    .chat-container {
        height: calc(100vh - 120px);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }
    
    .conversation-item {
        border-radius: 16px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: 2px solid transparent;
    }
    
    .conversation-item:hover {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border-color: #0ea5e9;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.15);
    }
    
    .conversation-item.active {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-color: #3b82f6;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.2);
    }
    
    .chat-sidebar {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-right: 1px solid #e2e8f0;
        box-shadow: 4px 0 15px rgba(0,0,0,0.05);
    }
    
    .chat-main {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
    }
    
    .message-bubble {
        max-width: 75%;
        border-radius: 20px;
        padding: 12px 18px;
        margin-bottom: 8px;
        word-wrap: break-word;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .message-sent {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 8px;
    }
    
    .message-received {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #374151;
        margin-right: auto;
        border-bottom-left-radius: 8px;
    }
    
    .online-indicator {
        width: 10px;
        height: 10px;
        background: #10b981;
        border-radius: 50%;
        border: 2px solid white;
        position: absolute;
        bottom: 0;
        right: 0;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    
    .typing-indicator {
        display: flex;
        padding: 12px 18px;
        background: #f3f4f6;
        border-radius: 20px;
        margin-bottom: 8px;
        max-width: 80px;
    }
    
    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #6b7280;
        margin: 0 2px;
        animation: typing 1.4s infinite ease-in-out;
    }
    
    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes typing {
        0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }
    
    .chat-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }
    
    .message-input {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 25px;
        padding: 12px 20px;
        transition: all 0.3s ease;
        resize: none;
        min-height: 50px;
        max-height: 120px;
    }
    
    .message-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
        transform: translateY(-2px);
    }
    
    .send-button {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
    }
    
    .send-button:hover {
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
    }
    
    .empty-state {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px dashed #0ea5e9;
        border-radius: 20px;
        padding: 3rem;
        text-align: center;
    }
    
    .search-box {
        background: rgba(255, 255, 255, 0.8);
        border: 2px solid #e2e8f0;
        border-radius: 15px;
        padding: 12px 16px;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .search-box:focus {
        outline: none;
        border-color: #3b82f6;
        background: white;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-blue-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">💬</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">المحادثات</h1>
                        <p class="text-sm text-gray-600">تواصل مع العملاء والدعم الفني</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 space-x-reverse">
                    <!-- New Message Button -->
                    <button onclick="showNewMessageModal()" 
                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>محادثة جديدة</span>
                    </button>
                    
                    <!-- Support Button -->
                    <a href="{{ route('chat.support.index') }}" 
                       class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>الدعم الفني</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Chat Interface -->
    <div class="chat-container flex">
        
        <!-- Conversations Sidebar -->
        <div class="w-1/3 chat-sidebar flex flex-col">
            
            <!-- Search & Filter -->
            <div class="p-4">
                <input type="text" 
                       id="conversation-search"
                       class="search-box w-full"
                       placeholder="البحث في المحادثات..."
                       oninput="searchConversations(this.value)">
                
                <div class="flex items-center space-x-3 space-x-reverse mt-3">
                    <select id="conversation-filter" 
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm bg-white"
                            onchange="filterConversations(this.value)">
                        <option value="all">جميع المحادثات</option>
                        <option value="customer_support">الدعم الفني</option>
                        <option value="merchant_customer">العملاء</option>
                        <option value="unread">غير مقروءة</option>
                    </select>
                </div>
            </div>

            <!-- Conversations List -->
            <div id="conversations-list" class="flex-1 overflow-y-auto px-4 pb-4">
                @forelse($conversations as $conversation)
                <div class="conversation-item p-4 mb-3 bg-white" 
                     data-conversation-id="{{ $conversation->id }}"
                     data-type="{{ $conversation->type }}"
                     onclick="selectConversation({{ $conversation->id }})">
                    
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <!-- Avatar -->
                        <div class="relative flex-shrink-0">
                            @php
                                $participant = $conversation->customer ?? $conversation->merchant ?? $conversation->supportAgent;
                                $avatar = $participant->avatar ?? null;
                            @endphp
                            
                            @if($avatar)
                                <img src="{{ asset('storage/' . $avatar) }}" 
                                     alt="{{ $participant->name }}" 
                                     class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-md">
                            @else
                                <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-purple-500 rounded-full flex items-center justify-center border-2 border-white shadow-md">
                                    <span class="text-white font-bold text-lg">
                                        {{ substr($participant->name ?? 'U', 0, 1) }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Online indicator (you can implement real-time status) -->
                            <div class="online-indicator"></div>
                        </div>

                        <!-- Conversation Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-1">
                                <h3 class="font-semibold text-gray-900 truncate">
                                    {{ $participant->name ?? 'مستخدم مجهول' }}
                                </h3>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    @if($conversation->latestMessage)
                                        <span class="text-xs text-gray-500">
                                            {{ $conversation->latestMessage->created_at->diffForHumans() }}
                                        </span>
                                    @endif
                                    
                                    <!-- Type Icon -->
                                    <span @class([
                                        'text-sm',
                                        'text-blue-500' => $conversation->type === 'customer_support',
                                        'text-green-500' => $conversation->type === 'merchant_customer'
                                    ])>
                                        @if($conversation->type === 'customer_support')
                                            🎧
                                        @elseif($conversation->type === 'merchant_customer')
                                            🏪
                                        @endif
                                    </span>
                                </div>
                            </div>
                            
                            @if($conversation->latestMessage)
                                <p class="text-sm text-gray-600 truncate mb-1">
                                    @if($conversation->latestMessage->type === 'file')
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                            </svg>
                                            ملف مرفق
                                        </span>
                                    @else
                                        {{ $conversation->latestMessage->content }}
                                    @endif
                                </p>
                            @endif
                            
                            <!-- Unread Count -->
                            @php
                                $unreadCount = $conversation->messages()
                                    ->where('sender_id', '!=', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            
                            @if($unreadCount > 0)
                                <div class="flex justify-end">
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-500 rounded-full">
                                        {{ $unreadCount }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <svg class="w-16 h-16 mx-auto mb-4 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">لا توجد محادثات</h3>
                    <p class="text-gray-500 text-sm mb-4">ابدأ محادثة جديدة للتواصل</p>
                    <button onclick="showNewMessageModal()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm">
                        بدء محادثة جديدة
                    </button>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 chat-main flex flex-col">
            
            <!-- Chat Placeholder (shown when no conversation is selected) -->
            <div id="chat-placeholder" class="flex-1 flex items-center justify-center">
                <div class="text-center">
                    <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <h3 class="text-xl font-medium text-gray-500 mb-2">اختر محادثة للبدء</h3>
                    <p class="text-gray-400">حدد محادثة من القائمة لعرض الرسائل</p>
                </div>
            </div>

            <!-- Active Chat (hidden by default) -->
            <div id="active-chat" class="flex-1 hidden">
                
                <!-- Chat Header -->
                <div class="chat-header flex items-center justify-between">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div id="chat-avatar" class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-bold">U</span>
                        </div>
                        <div>
                            <h3 id="chat-name" class="font-semibold">اسم المستخدم</h3>
                            <p id="chat-status" class="text-sm text-blue-100">متصل الآن</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <button onclick="toggleChatInfo()" 
                                class="p-2 hover:bg-white hover:bg-opacity-20 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        <button onclick="closeChat()" 
                                class="p-2 hover:bg-white hover:bg-opacity-20 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Messages Area -->
                <div id="messages-container" class="flex-1 overflow-y-auto p-6 space-y-4">
                    <!-- Messages will be loaded here dynamically -->
                </div>

                <!-- Typing Indicator -->
                <div id="typing-indicator" class="px-6 hidden">
                    <div class="typing-indicator">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="p-6 bg-white border-t border-gray-200">
                    <form id="message-form" class="flex items-end space-x-3 space-x-reverse">
                        <input type="hidden" id="current-conversation-id">
                        
                        <!-- File Upload -->
                        <div>
                            <input type="file" id="file-input" multiple class="hidden" onchange="handleFileUpload(this)">
                            <button type="button" 
                                    onclick="document.getElementById('file-input').click()"
                                    class="w-12 h-12 bg-gray-100 hover:bg-gray-200 rounded-full flex items-center justify-center transition-all">
                                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Message Input -->
                        <div class="flex-1">
                            <textarea id="message-input" 
                                      class="message-input w-full"
                                      placeholder="اكتب رسالتك هنا..."
                                      rows="1"
                                      onkeydown="handleKeyPress(event)"
                                      oninput="autoResize(this)"></textarea>
                        </div>

                        <!-- Send Button -->
                        <button type="submit" class="send-button">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div id="new-message-modal" class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold text-gray-800 mb-4">محادثة جديدة</h3>
        
        <form id="new-conversation-form">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">نوع المحادثة</label>
                <select id="conversation-type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="customer_support">دعم فني</option>
                    <option value="merchant_customer">عميل</option>
                </select>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">الرسالة الأولى</label>
                <textarea id="initial-message" 
                          class="w-full border border-gray-300 rounded-lg px-3 py-2" 
                          rows="3" 
                          placeholder="اكتب رسالتك هنا..."
                          required></textarea>
            </div>
            
            <div class="flex justify-end space-x-3 space-x-reverse">
                <button type="button" 
                        onclick="hideNewMessageModal()"
                        class="px-4 py-2 text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300 transition-all">
                    إلغاء
                </button>
                <button type="submit" 
                        class="px-4 py-2 text-white bg-blue-500 rounded-lg hover:bg-blue-600 transition-all">
                    بدء المحادثة
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
let currentConversationId = null;
let messagesContainer = null;

document.addEventListener('DOMContentLoaded', function() {
    messagesContainer = document.getElementById('messages-container');
    
    // Initialize message form
    document.getElementById('message-form').addEventListener('submit', sendMessage);
    document.getElementById('new-conversation-form').addEventListener('submit', createNewConversation);
});

function selectConversation(conversationId) {
    currentConversationId = conversationId;
    
    // Update UI
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    document.querySelector(`[data-conversation-id="${conversationId}"]`).classList.add('active');
    
    // Show chat area
    document.getElementById('chat-placeholder').classList.add('hidden');
    const activeChat = document.getElementById('active-chat');
    activeChat.classList.remove('hidden');
    activeChat.classList.add('flex', 'flex-col');
    document.getElementById('current-conversation-id').value = conversationId;
    
    // Load messages
    loadMessages(conversationId);
    
    // Load conversation details
    loadConversationDetails(conversationId);
}

async function loadMessages(conversationId) {
    try {
        const response = await fetch(`/chat/conversations/${conversationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            displayMessages(data.messages);
            markMessagesAsRead(conversationId);
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

function displayMessages(messages) {
    messagesContainer.innerHTML = '';
    
    messages.forEach(message => {
        const messageDiv = createMessageElement(message);
        messagesContainer.appendChild(messageDiv);
    });
    
    scrollToBottom();
}

function createMessageElement(message) {
    const div = document.createElement('div');
    const isOwn = message.sender_id === {{ auth()->id() }};
    
    div.className = `flex ${isOwn ? 'justify-end' : 'justify-start'}`;
    
    div.innerHTML = `
        <div class="message-bubble ${isOwn ? 'message-sent' : 'message-received'}">
            ${message.type === 'file' ? 
                `<div class="flex items-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <span>ملف مرفق</span>
                </div>` : 
                message.content
            }
            <div class="text-xs mt-1 opacity-70">
                ${new Date(message.created_at).toLocaleTimeString('ar-SA', {hour: '2-digit', minute: '2-digit'})}
            </div>
        </div>
    `;
    
    return div;
}

async function sendMessage(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if (!message || !currentConversationId) return;
    
    try {
        const response = await fetch(`/chat/conversations/${currentConversationId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                content: message,
                type: 'text'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            messageInput.value = '';
            autoResize(messageInput);
            loadMessages(currentConversationId);
        }
    } catch (error) {
        console.error('Error sending message:', error);
    }
}

async function createNewConversation(e) {
    e.preventDefault();
    
    const type = document.getElementById('conversation-type').value;
    const message = document.getElementById('initial-message').value;
    
    try {
        const response = await fetch('/chat/conversations/start', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                type: type,
                initial_message: message
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            hideNewMessageModal();
            window.location.reload(); // Reload to show new conversation
        }
    } catch (error) {
        console.error('Error creating conversation:', error);
    }
}

function loadConversationDetails(conversationId) {
    // This would load participant info and update the chat header
    // Implementation depends on your API structure
}

function markMessagesAsRead(conversationId) {
    // Mark messages as read
    fetch(`/chat/conversations/${conversationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}

function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
}

function handleKeyPress(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        document.getElementById('message-form').dispatchEvent(new Event('submit'));
    }
}

function showNewMessageModal() {
    const modal = document.getElementById('new-message-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function hideNewMessageModal() {
    const modal = document.getElementById('new-message-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.getElementById('new-conversation-form').reset();
}

function closeChat() {
    document.getElementById('chat-placeholder').classList.remove('hidden');
    document.getElementById('active-chat').classList.add('hidden');
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
    });
    currentConversationId = null;
}

function searchConversations(query) {
    const conversations = document.querySelectorAll('.conversation-item');
    conversations.forEach(conv => {
        const text = conv.textContent.toLowerCase();
        conv.style.display = text.includes(query.toLowerCase()) ? 'block' : 'none';
    });
}

function filterConversations(type) {
    const conversations = document.querySelectorAll('.conversation-item');
    conversations.forEach(conv => {
        if (type === 'all') {
            conv.style.display = 'block';
        } else {
            const convType = conv.dataset.type;
            conv.style.display = convType === type ? 'block' : 'none';
        }
    });
}

function handleFileUpload(input) {
    // Handle file upload implementation
    console.log('Files selected:', input.files);
}

function toggleChatInfo() {
    // Toggle chat info panel
    console.log('Toggle chat info');
}

// Auto-refresh messages every 5 seconds
setInterval(() => {
    if (currentConversationId) {
        loadMessages(currentConversationId);
    }
}, 5000);
</script>
@endpush
@endsection
