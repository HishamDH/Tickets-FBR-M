@extends('layouts.app')

@section('title', 'المحادثات - Chat')
@section('description', 'واجهة المحادثات والرسائل الفورية')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/creative-design-system.css') }}">
<style>
    .chat-container {
        height: calc(100vh - 120px);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
    }
    
    .conversation-item {
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .conversation-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(249, 115, 22, 0.1), transparent);
        transition: left 0.5s ease;
    }
    
    .conversation-item:hover::before {
        left: 100%;
    }
    
    .conversation-item:hover {
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        border-color: #f97316;
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 20px 40px rgba(249, 115, 22, 0.15);
    }
    
    .conversation-item.active {
        background: linear-gradient(135deg, #ea580c 0%, #dc2626 100%);
        border-color: #f97316;
        color: white;
        box-shadow: 0 15px 35px rgba(249, 115, 22, 0.3);
        transform: translateY(-3px);
    }
    
    .conversation-item.active .text-gray-600 {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    
    .conversation-item.active .text-gray-500 {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    
    .chat-sidebar {
        background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        border-right: 2px solid #e2e8f0;
        box-shadow: 4px 0 20px rgba(0,0,0,0.08);
        backdrop-filter: blur(10px);
    }
    
    .chat-main {
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        position: relative;
    }
    
    .chat-main::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="%23f97316" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
        pointer-events: none;
        z-index: 1;
    }
    
    .message-bubble {
        max-width: 75%;
        border-radius: 25px;
        padding: 16px 20px;
        margin-bottom: 12px;
        word-wrap: break-word;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        position: relative;
        animation: messageSlideIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        backdrop-filter: blur(10px);
    }
    
    @keyframes messageSlideIn {
        from {
            opacity: 0;
            transform: translateY(20px) scale(0.9);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .message-sent {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        margin-left: auto;
        border-bottom-right-radius: 8px;
        box-shadow: 0 10px 30px rgba(249, 115, 22, 0.3);
    }
    
    .message-sent::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 100%);
        border-radius: inherit;
        pointer-events: none;
    }
    
    .message-received {
        background: rgba(255, 255, 255, 0.9);
        color: #374151;
        margin-right: auto;
        border-bottom-left-radius: 8px;
        border: 1px solid rgba(249, 115, 22, 0.1);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    }
    
    .online-indicator {
        width: 12px;
        height: 12px;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        border: 3px solid white;
        position: absolute;
        bottom: -2px;
        right: -2px;
        animation: onlinePulse 2s infinite;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.5);
    }
    
    @keyframes onlinePulse {
        0% { 
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7),
                        0 0 10px rgba(16, 185, 129, 0.5); 
        }
        70% { 
            box-shadow: 0 0 0 15px rgba(16, 185, 129, 0),
                        0 0 20px rgba(16, 185, 129, 0.3); 
        }
        100% { 
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0),
                        0 0 10px rgba(16, 185, 129, 0.5); 
        }
    }
    
    .typing-indicator {
        display: flex;
        padding: 16px 20px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 25px;
        margin-bottom: 12px;
        max-width: 100px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(249, 115, 22, 0.1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    
    .typing-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        margin: 0 3px;
        animation: typingBounce 1.4s infinite ease-in-out;
    }
    
    .typing-dot:nth-child(1) { animation-delay: -0.32s; }
    .typing-dot:nth-child(2) { animation-delay: -0.16s; }
    
    @keyframes typingBounce {
        0%, 80%, 100% { 
            transform: scale(0.6) translateY(0); 
            opacity: 0.5; 
        }
        40% { 
            transform: scale(1) translateY(-10px); 
            opacity: 1; 
        }
    }
    
    .chat-header {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        border-radius: 25px 25px 0 0;
        padding: 2rem;
        box-shadow: 0 8px 30px rgba(249, 115, 22, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .chat-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: headerShine 4s linear infinite;
    }
    
    @keyframes headerShine {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .message-input {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(249, 115, 22, 0.2);
        border-radius: 25px;
        padding: 16px 24px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        resize: none;
        min-height: 60px;
        max-height: 140px;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    
    .message-input:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 6px rgba(249, 115, 22, 0.1),
                    0 15px 35px rgba(249, 115, 22, 0.15);
        transform: translateY(-3px);
        background: white;
    }
    
    .send-button {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border-radius: 50%;
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .send-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s ease;
    }
    
    .send-button:hover::before {
        left: 100%;
    }
    
    .send-button:hover {
        transform: translateY(-5px) scale(1.1);
        box-shadow: 0 15px 40px rgba(16, 185, 129, 0.4);
    }
    
    .send-button:active {
        transform: translateY(-2px) scale(1.05);
    }
    
    .empty-state {
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        border: 3px dashed #f97316;
        border-radius: 25px;
        padding: 4rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .empty-state::before {
        content: '';
        position: absolute;
        top: -2px;
        left: -2px;
        right: -2px;
        bottom: -2px;
        background: linear-gradient(45deg, #f97316, #ea580c, #f97316);
        border-radius: 25px;
        z-index: -1;
        animation: borderGlow 3s linear infinite;
    }
    
    @keyframes borderGlow {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .search-box {
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(249, 115, 22, 0.2);
        border-radius: 20px;
        padding: 16px 20px;
        backdrop-filter: blur(15px);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    }
    
    .search-box:focus {
        outline: none;
        border-color: #f97316;
        background: white;
        box-shadow: 0 0 0 6px rgba(249, 115, 22, 0.1),
                    0 15px 35px rgba(249, 115, 22, 0.15);
        transform: translateY(-3px);
    }
    
    .chat-particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 1;
    }
    
    .particle {
        position: absolute;
        width: 4px;
        height: 4px;
        background: #f97316;
        border-radius: 50%;
        opacity: 0.3;
        animation: floatParticle 6s linear infinite;
    }
    
    @keyframes floatParticle {
        0% {
            transform: translateY(100vh) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 0.3;
        }
        90% {
            opacity: 0.3;
        }
        100% {
            transform: translateY(-10vh) rotate(360deg);
            opacity: 0;
        }
    }
    
    .avatar-ring {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        padding: 3px;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(249, 115, 22, 0.3);
    }
    
    .avatar-inner {
        border-radius: 50%;
        overflow: hidden;
        background: white;
    }
    
    /* Enhanced Unread Badge */
    .unread-badge {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        animation: badgePulse 2s infinite;
        border: 2px solid white;
    }
    
    @keyframes badgePulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }
    
    /* Smooth scroll for messages */
    #messages-container {
        scroll-behavior: smooth;
    }
    
    /* Enhanced file upload button */
    .file-upload-btn {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 6px 20px rgba(139, 92, 246, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .file-upload-btn:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 10px 30px rgba(139, 92, 246, 0.4);
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
                            
                            <div class="avatar-ring">
                                @if($avatar)
                                    <div class="avatar-inner w-14 h-14">
                                        <img src="{{ asset('storage/' . $avatar) }}" 
                                             alt="{{ $participant->name }}" 
                                             class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="avatar-inner w-14 h-14 bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center">
                                        <span class="text-white font-bold text-xl">
                                            {{ substr($participant->name ?? 'U', 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Enhanced Online indicator -->
                            <div class="online-indicator"></div>
                            
                            <!-- Conversation Type Badge -->
                            <div class="absolute -top-1 -right-1 w-6 h-6 rounded-full flex items-center justify-center text-xs {{ $conversation->type === 'customer_support' ? 'bg-blue-500' : 'bg-green-500' }} text-white shadow-lg">
                                @if($conversation->type === 'customer_support')
                                    🎧
                                @elseif($conversation->type === 'merchant_customer')
                                    🏪
                                @endif
                            </div>
                        </div>

                        <!-- Conversation Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-bold text-gray-900 truncate text-lg">
                                    {{ $participant->name ?? 'مستخدم مجهول' }}
                                </h3>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    @if($conversation->latestMessage)
                                        <span class="text-xs text-gray-500 font-medium">
                                            {{ $conversation->latestMessage->created_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($conversation->latestMessage)
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 truncate mb-1 flex-1">
                                        @if($conversation->latestMessage->type === 'file')
                                            <span class="flex items-center text-purple-600">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                </svg>
                                                <span class="font-medium">ملف مرفق</span>
                                            </span>
                                        @else
                                            {{ Str::limit($conversation->latestMessage->content, 40) }}
                                        @endif
                                    </p>
                                    
                                    <!-- Message Status Icon -->
                                    @if($conversation->latestMessage->sender_id === auth()->id())
                                        <div class="ml-2 text-green-500">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            <!-- Enhanced Unread Count -->
                            @php
                                $unreadCount = $conversation->messages()
                                    ->where('sender_id', '!=', auth()->id())
                                    ->where('is_read', false)
                                    ->count();
                            @endphp
                            
                            @if($unreadCount > 0)
                                <div class="flex justify-end mt-2">
                                    <span class="unread-badge">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Conversation Tags -->
                            <div class="flex items-center justify-between mt-2">
                                <div class="flex space-x-1 space-x-reverse">
                                    @if($conversation->priority === 'high')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            🔥 عاجل
                                        </span>
                                    @endif
                                    
                                    @if($conversation->type === 'customer_support')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            دعم فني
                                        </span>
                                    @elseif($conversation->type === 'merchant_customer')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            عميل
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Quick Actions -->
                                <div class="flex space-x-1 space-x-reverse opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button class="w-6 h-6 bg-orange-100 hover:bg-orange-200 rounded-full flex items-center justify-center transition-colors">
                                        <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
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
            <div id="chat-placeholder" class="flex-1 flex items-center justify-center relative">
                <!-- Animated Particles Background -->
                <div class="chat-particles">
                    <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
                    <div class="particle" style="left: 20%; animation-delay: 1s;"></div>
                    <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
                    <div class="particle" style="left: 40%; animation-delay: 3s;"></div>
                    <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
                    <div class="particle" style="left: 60%; animation-delay: 5s;"></div>
                    <div class="particle" style="left: 70%; animation-delay: 0.5s;"></div>
                    <div class="particle" style="left: 80%; animation-delay: 1.5s;"></div>
                    <div class="particle" style="left: 90%; animation-delay: 2.5s;"></div>
                </div>
                
                <div class="text-center relative z-10">
                    <!-- Animated Icon -->
                    <div class="relative mb-8">
                        <div class="w-32 h-32 mx-auto bg-gradient-to-r from-orange-100 to-orange-200 rounded-full flex items-center justify-center mb-4 shadow-2xl animate-float">
                            <svg class="w-16 h-16 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        
                        <!-- Floating Decorative Elements -->
                        <div class="absolute -top-4 -right-4 w-8 h-8 bg-orange-300 rounded-full opacity-60 animate-bounce" style="animation-delay: 0.1s;"></div>
                        <div class="absolute -top-2 -left-6 w-6 h-6 bg-orange-400 rounded-full opacity-40 animate-bounce" style="animation-delay: 0.3s;"></div>
                        <div class="absolute -bottom-3 right-2 w-4 h-4 bg-orange-500 rounded-full opacity-50 animate-bounce" style="animation-delay: 0.5s;"></div>
                        <div class="absolute -bottom-6 -left-2 w-5 h-5 bg-orange-300 rounded-full opacity-30 animate-bounce" style="animation-delay: 0.7s;"></div>
                    </div>
                    
                    <h3 class="text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-orange-500 to-red-500 mb-4 animate-pulse">
                        اختر محادثة للبدء 💬
                    </h3>
                    <p class="text-lg text-gray-500 mb-6 max-w-md mx-auto leading-relaxed">
                        حدد محادثة من القائمة لعرض الرسائل والاستمتاع بتجربة محادثة محسنة
                    </p>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-center space-x-4 space-x-reverse">
                        <button onclick="showNewMessageModal()" 
                                class="btn-primary px-6 py-3 bg-gradient-to-r from-orange-500 to-red-500 text-white rounded-full hover:shadow-lg transform hover:scale-105 transition-all duration-300 flex items-center space-x-2 space-x-reverse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span>محادثة جديدة</span>
                        </button>
                        
                        <a href="{{ route('chat.support.index') }}" 
                           class="btn-secondary px-6 py-3 bg-white border-2 border-orange-300 text-orange-600 rounded-full hover:bg-orange-50 transform hover:scale-105 transition-all duration-300 flex items-center space-x-2 space-x-reverse">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>الدعم الفني</span>
                        </a>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="mt-12 grid grid-cols-3 gap-6 max-w-lg mx-auto">
                        <div class="text-center p-4 bg-white bg-opacity-60 rounded-2xl backdrop-blur-sm">
                            <div class="text-2xl font-bold text-orange-500">{{ $conversations->count() }}</div>
                            <div class="text-sm text-gray-600">محادثة</div>
                        </div>
                        <div class="text-center p-4 bg-white bg-opacity-60 rounded-2xl backdrop-blur-sm">
                            <div class="text-2xl font-bold text-green-500">🟢</div>
                            <div class="text-sm text-gray-600">متصل</div>
                        </div>
                        <div class="text-center p-4 bg-white bg-opacity-60 rounded-2xl backdrop-blur-sm">
                            <div class="text-2xl font-bold text-blue-500">24/7</div>
                            <div class="text-sm text-gray-600">دعم</div>
                        </div>
                    </div>
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
                <div class="p-6 bg-gradient-to-r from-white to-gray-50 border-t-2 border-orange-100 relative">
                    <!-- Enhanced Input Container -->
                    <div class="relative">
                        <!-- Decorative Background -->
                        <div class="absolute inset-0 bg-gradient-to-r from-orange-50 to-red-50 rounded-3xl opacity-50"></div>
                        
                        <form id="message-form" class="flex items-end space-x-4 space-x-reverse relative z-10 p-4">
                            <input type="hidden" id="current-conversation-id">
                            
                            <!-- Enhanced File Upload -->
                            <div class="relative">
                                <input type="file" id="file-input" multiple class="hidden" onchange="handleFileUpload(this)">
                                <button type="button" 
                                        onclick="document.getElementById('file-input').click()"
                                        class="file-upload-btn group">
                                    <svg class="w-6 h-6 text-white group-hover:rotate-12 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                    
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-1 bg-gray-800 text-white text-xs rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-300 whitespace-nowrap">
                                        إرفاق ملف
                                    </div>
                                </button>
                            </div>

                            <!-- Enhanced Message Input -->
                            <div class="flex-1 relative">
                                <textarea id="message-input" 
                                          class="message-input w-full pr-12"
                                          placeholder="✨ اكتب رسالتك هنا واضغط Enter للإرسال..."
                                          rows="1"
                                          onkeydown="handleKeyPress(event)"
                                          oninput="autoResize(this)"></textarea>
                                
                                <!-- Emoji Button -->
                                <button type="button" 
                                        onclick="toggleEmojiPicker()"
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 w-8 h-8 bg-orange-100 hover:bg-orange-200 rounded-full flex items-center justify-center transition-all duration-300 group">
                                    <span class="text-lg group-hover:scale-110 transition-transform duration-300">😊</span>
                                </button>
                                
                                <!-- Character Counter -->
                                <div class="absolute bottom-2 right-3 text-xs text-gray-400">
                                    <span id="char-counter">0</span>/500
                                </div>
                            </div>

                            <!-- Enhanced Send Button -->
                            <button type="submit" class="send-button group">
                                <svg class="w-6 h-6 transform group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                
                                <!-- Send Button Ring Effect -->
                                <div class="absolute inset-0 rounded-full border-2 border-green-300 scale-100 group-hover:scale-110 opacity-0 group-hover:opacity-100 transition-all duration-300"></div>
                            </button>
                            
                            <!-- Voice Message Button -->
                            <button type="button" 
                                    class="w-14 h-14 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white transition-all duration-300 hover:shadow-lg transform hover:scale-105 group">
                                <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                                </svg>
                            </button>
                        </form>
                        
                        <!-- Quick Actions Row -->
                        <div class="flex items-center justify-between px-4 pb-2">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <!-- Quick Emojis -->
                                <div class="flex space-x-2 space-x-reverse">
                                    <button onclick="insertEmoji('👍')" class="w-8 h-8 hover:bg-orange-100 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">👍</button>
                                    <button onclick="insertEmoji('❤️')" class="w-8 h-8 hover:bg-orange-100 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">❤️</button>
                                    <button onclick="insertEmoji('😊')" class="w-8 h-8 hover:bg-orange-100 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">😊</button>
                                    <button onclick="insertEmoji('🎉')" class="w-8 h-8 hover:bg-orange-100 rounded-full flex items-center justify-center transition-all duration-300 hover:scale-110">🎉</button>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 space-x-reverse text-xs text-gray-500">
                                <div class="flex items-center space-x-1 space-x-reverse">
                                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                    <span>متصل</span>
                                </div>
                                <span>•</span>
                                <span>آخر ظهور منذ دقيقة</span>
                            </div>
                        </div>
                    </div>
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
<script src="{{ asset('js/creative-interactions.js') }}"></script>
<script>
let currentConversationId = null;
let messagesContainer = null;
let isTyping = false;
let typingTimeout = null;

document.addEventListener('DOMContentLoaded', function() {
    messagesContainer = document.getElementById('messages-container');
    
    // Initialize message form
    document.getElementById('message-form').addEventListener('submit', sendMessage);
    document.getElementById('new-conversation-form').addEventListener('submit', createNewConversation);
    
    // Initialize character counter
    const messageInput = document.getElementById('message-input');
    messageInput.addEventListener('input', updateCharCounter);
    
    // Initialize creative interactions
    initializeChatAnimations();
    
    // Initialize emoji functionality
    initializeEmojiSupport();
    
    console.log('🎨 Enhanced Chat System - Ready! ✨');
});

function initializeChatAnimations() {
    // Add hover effects to conversation items
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px) scale(1.02)';
        });
        
        item.addEventListener('mouseleave', function() {
            if (!this.classList.contains('active')) {
                this.style.transform = 'translateY(0) scale(1)';
            }
        });
    });
    
    // Add sparkle effect to send button
    const sendButton = document.querySelector('.send-button');
    if (sendButton) {
        sendButton.addEventListener('click', function() {
            createSparkleEffect(this);
        });
    }
}

function createSparkleEffect(element) {
    for (let i = 0; i < 6; i++) {
        setTimeout(() => {
            const sparkle = document.createElement('div');
            sparkle.innerHTML = '✨';
            sparkle.style.position = 'absolute';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.userSelect = 'none';
            sparkle.style.fontSize = Math.random() * 10 + 10 + 'px';
            sparkle.style.left = Math.random() * 100 + '%';
            sparkle.style.top = Math.random() * 100 + '%';
            sparkle.style.animation = 'sparkleFloat 1s ease-out forwards';
            
            element.appendChild(sparkle);
            
            setTimeout(() => sparkle.remove(), 1000);
        }, i * 100);
    }
}

function initializeEmojiSupport() {
    // Quick emoji insertion
    window.insertEmoji = function(emoji) {
        const messageInput = document.getElementById('message-input');
        const currentValue = messageInput.value;
        const newValue = currentValue + emoji;
        messageInput.value = newValue;
        messageInput.focus();
        updateCharCounter();
        
        // Add animation to the clicked emoji button
        event.target.style.animation = 'emojiPop 0.3s ease-out';
        setTimeout(() => {
            event.target.style.animation = '';
        }, 300);
    };
}

function updateCharCounter() {
    const messageInput = document.getElementById('message-input');
    const counter = document.getElementById('char-counter');
    const currentLength = messageInput.value.length;
    const maxLength = 500;
    
    counter.textContent = currentLength;
    
    if (currentLength > maxLength * 0.8) {
        counter.style.color = '#f59e0b';
    } else if (currentLength > maxLength * 0.9) {
        counter.style.color = '#ef4444';
    } else {
        counter.style.color = '#9ca3af';
    }
    
    // Disable send button if too long
    const sendButton = document.querySelector('.send-button');
    if (currentLength > maxLength) {
        sendButton.disabled = true;
        sendButton.style.opacity = '0.5';
    } else {
        sendButton.disabled = false;
        sendButton.style.opacity = '1';
    }
}

function selectConversation(conversationId) {
    currentConversationId = conversationId;
    
    // Update UI with enhanced animations
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
        item.style.transform = 'translateY(0) scale(1)';
    });
    
    const selectedConversation = document.querySelector(`[data-conversation-id="${conversationId}"]`);
    selectedConversation.classList.add('active');
    selectedConversation.style.transform = 'translateY(-3px) scale(1.02)';
    
    // Show chat area with animation
    const placeholder = document.getElementById('chat-placeholder');
    const activeChat = document.getElementById('active-chat');
    
    placeholder.style.animation = 'fadeOut 0.3s ease-out';
    setTimeout(() => {
        placeholder.classList.add('hidden');
        activeChat.classList.remove('hidden');
        activeChat.classList.add('flex', 'flex-col');
        activeChat.style.animation = 'fadeIn 0.5s ease-out';
    }, 300);
    
    document.getElementById('current-conversation-id').value = conversationId;
    
    // Load messages with loading animation
    showLoadingMessages();
    loadMessages(conversationId);
    loadConversationDetails(conversationId);
}

function showLoadingMessages() {
    messagesContainer.innerHTML = `
        <div class="flex justify-center py-8">
            <div class="relative">
                <div class="w-12 h-12 border-4 border-orange-200 rounded-full"></div>
                <div class="w-12 h-12 border-4 border-orange-500 rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-orange-500 text-lg animate-pulse">💬</span>
                </div>
            </div>
        </div>
    `;
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
            setTimeout(() => {
                displayMessages(data.messages);
                markMessagesAsRead(conversationId);
            }, 500); // Delay for loading animation
        }
    } catch (error) {
        console.error('Error loading messages:', error);
        showNotification('حدث خطأ في تحميل الرسائل', 'error');
    }
}

function displayMessages(messages) {
    messagesContainer.innerHTML = '';
    
    messages.forEach((message, index) => {
        setTimeout(() => {
            const messageDiv = createMessageElement(message);
            messagesContainer.appendChild(messageDiv);
            
            if (index === messages.length - 1) {
                scrollToBottom();
            }
        }, index * 100); // Stagger message appearance
    });
}

function createMessageElement(message) {
    const div = document.createElement('div');
    const isOwn = message.sender_id === {{ auth()->id() }};
    
    div.className = `flex ${isOwn ? 'justify-end' : 'justify-start'} mb-4`;
    div.style.opacity = '0';
    div.style.animation = 'messageSlideIn 0.5s ease-out forwards';
    
    div.innerHTML = `
        <div class="message-bubble ${isOwn ? 'message-sent' : 'message-received'} relative group">
            ${message.type === 'file' ? 
                `<div class="flex items-center space-x-3 space-x-reverse">
                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium">ملف مرفق</div>
                        <div class="text-xs opacity-70">انقر للتحميل</div>
                    </div>
                </div>` : 
                `<div class="whitespace-pre-wrap">${message.content}</div>`
            }
            <div class="flex items-center justify-between mt-2 text-xs opacity-70">
                <span>${new Date(message.created_at).toLocaleTimeString('ar-SA', {hour: '2-digit', minute: '2-digit'})}</span>
                ${isOwn ? '<div class="text-green-300"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>' : ''}
            </div>
            
            <!-- Message Actions (hidden by default) -->
            <div class="absolute top-0 ${isOwn ? 'left-0 -translate-x-full' : 'right-0 translate-x-full'} opacity-0 group-hover:opacity-100 transition-all duration-300 flex space-x-1 space-x-reverse bg-white rounded-lg shadow-lg p-1 mr-2 ml-2">
                <button onclick="reactToMessage('👍')" class="w-6 h-6 hover:bg-gray-100 rounded flex items-center justify-center text-sm">👍</button>
                <button onclick="reactToMessage('❤️')" class="w-6 h-6 hover:bg-gray-100 rounded flex items-center justify-center text-sm">❤️</button>
                <button onclick="replyToMessage(${message.id})" class="w-6 h-6 hover:bg-gray-100 rounded flex items-center justify-center">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                    </svg>
                </button>
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
    
    // Add sending animation
    const sendButton = document.querySelector('.send-button');
    sendButton.style.animation = 'pulse 0.5s ease-out';
    createSparkleEffect(sendButton);
    
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
            updateCharCounter();
            loadMessages(currentConversationId);
            showNotification('تم إرسال الرسالة بنجاح! 📩', 'success');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        showNotification('حدث خطأ في إرسال الرسالة', 'error');
    } finally {
        setTimeout(() => {
            sendButton.style.animation = '';
        }, 500);
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
            showNotification('تم إنشاء المحادثة بنجاح! 🎉', 'success');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    } catch (error) {
        console.error('Error creating conversation:', error);
        showNotification('حدث خطأ في إنشاء المحادثة', 'error');
    }
}

function loadConversationDetails(conversationId) {
    // Load participant info and update chat header with animation
    const chatName = document.getElementById('chat-name');
    const chatStatus = document.getElementById('chat-status');
    
    if (chatName) {
        chatName.style.animation = 'fadeInUp 0.5s ease-out';
    }
    if (chatStatus) {
        chatStatus.style.animation = 'fadeInUp 0.5s ease-out 0.2s both';
    }
}

function markMessagesAsRead(conversationId) {
    fetch(`/chat/conversations/${conversationId}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
}

function scrollToBottom() {
    messagesContainer.scrollTo({
        top: messagesContainer.scrollHeight,
        behavior: 'smooth'
    });
}

function autoResize(textarea) {
    textarea.style.height = 'auto';
    textarea.style.height = Math.min(textarea.scrollHeight, 140) + 'px';
}

function handleKeyPress(event) {
    if (event.key === 'Enter' && !event.shiftKey) {
        event.preventDefault();
        document.getElementById('message-form').dispatchEvent(new Event('submit'));
    }
    
    // Show typing indicator
    if (!isTyping) {
        isTyping = true;
        // You can send typing status to server here
    }
    
    clearTimeout(typingTimeout);
    typingTimeout = setTimeout(() => {
        isTyping = false;
        // You can send stop typing status to server here
    }, 1000);
}

function showNewMessageModal() {
    const modal = document.getElementById('new-message-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.style.animation = 'modalSlideIn 0.3s ease-out';
}

function hideNewMessageModal() {
    const modal = document.getElementById('new-message-modal');
    modal.style.animation = 'modalSlideOut 0.3s ease-out';
    setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('new-conversation-form').reset();
    }, 300);
}

function closeChat() {
    const placeholder = document.getElementById('chat-placeholder');
    const activeChat = document.getElementById('active-chat');
    
    activeChat.style.animation = 'fadeOut 0.3s ease-out';
    setTimeout(() => {
        activeChat.classList.add('hidden');
        placeholder.classList.remove('hidden');
        placeholder.style.animation = 'fadeIn 0.5s ease-out';
    }, 300);
    
    document.querySelectorAll('.conversation-item').forEach(item => {
        item.classList.remove('active');
        item.style.transform = 'translateY(0) scale(1)';
    });
    currentConversationId = null;
}

function searchConversations(query) {
    const conversations = document.querySelectorAll('.conversation-item');
    conversations.forEach(conv => {
        const text = conv.textContent.toLowerCase();
        const shouldShow = text.includes(query.toLowerCase());
        
        if (shouldShow) {
            conv.style.display = 'block';
            conv.style.animation = 'fadeIn 0.3s ease-out';
        } else {
            conv.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                conv.style.display = 'none';
            }, 300);
        }
    });
}

function filterConversations(type) {
    const conversations = document.querySelectorAll('.conversation-item');
    conversations.forEach(conv => {
        const convType = conv.dataset.type;
        const shouldShow = type === 'all' || convType === type;
        
        if (shouldShow) {
            conv.style.display = 'block';
            conv.style.animation = 'fadeIn 0.3s ease-out';
        } else {
            conv.style.animation = 'fadeOut 0.3s ease-out';
            setTimeout(() => {
                conv.style.display = 'none';
            }, 300);
        }
    });
}

function handleFileUpload(input) {
    const files = Array.from(input.files);
    files.forEach(file => {
        showNotification(`تم اختيار الملف: ${file.name} 📎`, 'info');
    });
    console.log('Files selected:', files);
}

function toggleChatInfo() {
    showNotification('معلومات المحادثة قريباً! 📋', 'info');
}

function toggleEmojiPicker() {
    showNotification('منتقي الرموز التعبيرية قريباً! 😊', 'info');
}

function reactToMessage(emoji) {
    showNotification(`تفاعل بـ ${emoji}`, 'success');
}

function replyToMessage(messageId) {
    showNotification('الرد على الرسالة قريباً! 💬', 'info');
}

// Enhanced notification function
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 transition-all duration-300 transform translate-x-full`;
    
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        info: 'bg-blue-500 text-white',
        warning: 'bg-yellow-500 text-black'
    };
    
    notification.className += ` ${colors[type]}`;
    notification.innerHTML = `
        <div class="flex items-center space-x-3 space-x-reverse">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        notification.style.transform = 'translateX(full)';
        setTimeout(() => notification.remove(), 300);
    }, 4000);
}

// Auto-refresh messages every 10 seconds
setInterval(() => {
    if (currentConversationId) {
        // Load messages silently without loading animation
        fetch(`/chat/conversations/${currentConversationId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Only update if there are new messages
                const currentMessageCount = messagesContainer.children.length;
                if (data.messages.length > currentMessageCount) {
                    displayMessages(data.messages);
                }
            }
        })
        .catch(error => console.error('Auto-refresh error:', error));
    }
}, 10000);

// Additional CSS animations
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-20px); }
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes modalSlideIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    
    @keyframes modalSlideOut {
        from { opacity: 1; transform: scale(1); }
        to { opacity: 0; transform: scale(0.9); }
    }
    
    @keyframes emojiPop {
        0% { transform: scale(1); }
        50% { transform: scale(1.3); }
        100% { transform: scale(1); }
    }
    
    @keyframes sparkleFloat {
        0% { opacity: 1; transform: translateY(0) scale(1); }
        100% { opacity: 0; transform: translateY(-20px) scale(0); }
    }
`;
document.head.appendChild(additionalStyles);
</script>
@endpush
@endsection
