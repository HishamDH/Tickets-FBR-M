@extends('layouts.app')

@section('title', 'الدعم الفني - Technical Support')
@section('description', 'تواصل مع فريق الدعم الفني للحصول على المساعدة')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/creative-design-system.css') }}">
<style>
    .support-container {
        min-height: calc(100vh - 120px);
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        position: relative;
        overflow: hidden;
    }
    
    .support-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="support-grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="%23f97316" stroke-width="0.5" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23support-grid)"/></svg>');
        pointer-events: none;
        z-index: 1;
    }
    
    .support-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 30px;
        box-shadow: 0 25px 50px rgba(249, 115, 22, 0.15);
        border: 2px solid rgba(249, 115, 22, 0.1);
        backdrop-filter: blur(20px);
        position: relative;
        z-index: 10;
        overflow: hidden;
    }
    
    .support-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, rgba(249, 115, 22, 0.02) 25%, transparent 25%, transparent 75%, rgba(249, 115, 22, 0.02) 75%);
        background-size: 30px 30px;
        pointer-events: none;
    }
    
    .support-header {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        border-radius: 30px 30px 0 0;
        padding: 3rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .support-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: conic-gradient(from 0deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: headerRotate 8s linear infinite;
    }
    
    .support-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 20px;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none"><path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" fill="white"/></svg>');
        background-size: cover;
    }
    
    @keyframes headerRotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .priority-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(10px);
        position: relative;
        overflow: hidden;
    }
    
    .priority-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        animation: badgeShine 2s infinite;
    }
    
    @keyframes badgeShine {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .priority-low { 
        color: #10b981; 
        border-color: #10b981;
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(16, 185, 129, 0.2));
    }
    .priority-medium { 
        color: #f59e0b; 
        border-color: #f59e0b;
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.1), rgba(245, 158, 11, 0.2));
    }
    .priority-high { 
        color: #ef4444; 
        border-color: #ef4444;
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.2));
    }
    .priority-urgent { 
        color: #dc2626; 
        border-color: #dc2626;
        background: linear-gradient(135deg, rgba(220, 38, 38, 0.1), rgba(220, 38, 38, 0.2));
        animation: urgentPulse 1s infinite alternate;
    }
    
    @keyframes urgentPulse {
        0% { box-shadow: 0 0 10px rgba(220, 38, 38, 0.5); }
        100% { box-shadow: 0 0 20px rgba(220, 38, 38, 0.8); }
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin: 3rem 0;
    }
    
    .quick-action {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 2px solid rgba(249, 115, 22, 0.1);
        border-radius: 20px;
        padding: 2rem;
        text-center;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transform: translateY(0) scale(1);
    }
    
    .quick-action::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(249, 115, 22, 0.1), transparent);
        transition: left 0.6s ease;
    }
    
    .quick-action:hover::before {
        left: 100%;
    }
    
    .quick-action:hover {
        border-color: #f97316;
        background: linear-gradient(135deg, #fff7ed 0%, #fed7aa 100%);
        transform: translateY(-8px) scale(1.05);
        box-shadow: 0 20px 40px rgba(249, 115, 22, 0.2);
    }
    
    .quick-action .action-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
        filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        transition: all 0.3s ease;
    }
    
    .quick-action:hover .action-icon {
        transform: scale(1.2) rotate(5deg);
        filter: drop-shadow(0 8px 16px rgba(249, 115, 22, 0.3));
    }
    
    .faq-item {
        background: rgba(255, 255, 255, 0.8);
        border: 2px solid rgba(249, 115, 22, 0.1);
        border-radius: 20px;
        margin-bottom: 1.5rem;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        backdrop-filter: blur(10px);
    }
    
    .faq-item:hover {
        box-shadow: 0 15px 35px rgba(249, 115, 22, 0.15);
        transform: translateY(-3px);
        border-color: #f97316;
    }
    
    .faq-question {
        padding: 1.5rem 2rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 700;
        color: #374151;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .faq-question::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(to bottom, #f97316, #ea580c);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .faq-item:hover .faq-question::before,
    .faq-item.active .faq-question::before {
        opacity: 1;
    }
    
    .faq-question .toggle-icon {
        transition: transform 0.3s ease;
        color: #f97316;
    }
    
    .faq-item.active .faq-question .toggle-icon {
        transform: rotate(180deg);
    }
    
    .faq-answer {
        padding: 1.5rem 2rem;
        color: #6b7280;
        line-height: 1.8;
        display: none;
        background: rgba(255, 255, 255, 0.5);
    }
    
    .faq-item.active .faq-answer {
        display: block;
        animation: faqSlideDown 0.4s ease-out;
    }
    
    @keyframes faqSlideDown {
        from { 
            opacity: 0; 
            transform: translateY(-10px);
            max-height: 0;
        }
        to { 
            opacity: 1; 
            transform: translateY(0);
            max-height: 200px;
        }
    }
    
    .chat-interface {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 20px;
        overflow: hidden;
        border: 2px solid rgba(249, 115, 22, 0.1);
    }
    
    .chat-messages {
        max-height: 400px;
        overflow-y: auto;
        padding: 1.5rem;
        background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
        scroll-behavior: smooth;
    }
    
    .support-message {
        margin-bottom: 1rem;
        animation: messageSlideIn 0.5s ease-out;
    }
    
    .message-user {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 20px 20px 5px 20px;
        margin-left: 20%;
        box-shadow: 0 8px 25px rgba(249, 115, 22, 0.3);
        position: relative;
    }
    
    .message-user::after {
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
    
    .message-support {
        background: rgba(255, 255, 255, 0.9);
        color: #374151;
        padding: 1rem 1.5rem;
        border-radius: 20px 20px 20px 5px;
        margin-right: 20%;
        border: 2px solid rgba(249, 115, 22, 0.1);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        backdrop-filter: blur(10px);
    }
    
    .typing-animation {
        display: flex;
        align-items: center;
        padding: 1rem 1.5rem;
        background: rgba(249, 115, 22, 0.1);
        border-radius: 20px;
        margin-right: 20%;
        border: 2px solid rgba(249, 115, 22, 0.2);
    }
    
    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #f97316;
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
    
    .form-input {
        width: 100%;
        padding: 1rem 1.5rem;
        border: 2px solid rgba(249, 115, 22, 0.2);
        border-radius: 15px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }
    
    .form-input:focus {
        outline: none;
        border-color: #f97316;
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        transform: translateY(-2px);
        background: white;
    }
    
    .floating-particles {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 2;
    }
    
    .particle {
        position: absolute;
        width: 6px;
        height: 6px;
        background: #f97316;
        border-radius: 50%;
        opacity: 0.3;
        animation: floatParticle 8s linear infinite;
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
    
    /* Enhanced buttons */
    .btn-primary-enhanced {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        color: white;
        padding: 1rem 2rem;
        border-radius: 25px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 25px rgba(249, 115, 22, 0.3);
    }
    
    .btn-primary-enhanced::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s ease;
    }
    
    .btn-primary-enhanced:hover::before {
        left: 100%;
    }
    
    .btn-primary-enhanced:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 40px rgba(249, 115, 22, 0.4);
    }
    
    .btn-primary-enhanced:active {
        transform: translateY(-1px) scale(1.02);
    }
</style>
@endpush
        letter-spacing: 0.5px;
    }
    
    .priority-low {
        background: #dcfce7;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }
    
    .priority-medium {
        background: #fef3c7;
        color: #d97706;
        border: 1px solid #fed7aa;
    }
    
    .priority-high {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }
    
    .priority-urgent {
        background: #fdf2f8;
        color: #be185d;
        border: 1px solid #f9a8d4;
        animation: pulse 2s infinite;
    }
    
    .support-option {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .support-option:hover {
        border-color: #3b82f6;
        transform: translateY(-4px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.15);
    }
    
    .support-option::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(59, 130, 246, 0.1), transparent);
        transition: left 0.5s;
    }
    
    .support-option:hover::before {
        left: 100%;
    }
    
    .chat-interface {
        max-height: 600px;
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    
    .chat-messages {
        height: 400px;
        overflow-y: auto;
        padding: 1rem;
        background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
    }
    
    .support-message {
        max-width: 80%;
        margin-bottom: 1rem;
        animation: messageSlide 0.3s ease-out;
    }
    
    @keyframes messageSlide {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .message-support {
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0c4a6e;
        border-radius: 20px 20px 20px 5px;
        padding: 12px 18px;
        margin-right: auto;
        border: 1px solid #7dd3fc;
    }
    
    .message-user {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        color: white;
        border-radius: 20px 20px 5px 20px;
        padding: 12px 18px;
        margin-left: auto;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    
    .typing-animation {
        display: flex;
        align-items: center;
        padding: 12px 18px;
        background: #f3f4f6;
        border-radius: 20px;
        margin-bottom: 1rem;
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
    
    .form-input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 16px;
        transition: all 0.3s ease;
        background: #ffffff;
    }
    
    .form-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        transform: translateY(-2px);
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin: 2rem 0;
    }
    
    .quick-action {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        text-center;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .quick-action:hover {
        border-color: #3b82f6;
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        transform: translateY(-2px);
    }
    
    .faq-item {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .faq-item:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .faq-question {
        padding: 1rem 1.5rem;
        background: #f8fafc;
        cursor: pointer;
        display: flex;
        justify-content: between;
        align-items: center;
        font-weight: 600;
        color: #374151;
    }
    
    .faq-answer {
        padding: 1rem 1.5rem;
        color: #6b7280;
        line-height: 1.6;
        display: none;
    }
    
    .faq-item.active .faq-answer {
        display: block;
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="support-container">
    
    <!-- Header -->
    <div class="bg-white shadow-lg border-b-4 border-blue-500 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-cyan-500 rounded-xl flex items-center justify-center">
                        <span class="text-white text-2xl">🎧</span>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">الدعم الفني</h1>
                        <p class="text-sm text-gray-600">نحن هنا لمساعدتك في أي وقت</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3 space-x-reverse">
                    <!-- Back to Chat -->
                    <a href="{{ route('chat.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center space-x-2 space-x-reverse transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        <span>العودة للمحادثات</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Success Message -->
        @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="mr-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
        @endif

        @if($supportConversation)
        <!-- Existing Support Conversation -->
        <div class="support-card mb-8">
            <div class="support-header">
                <h2 class="text-2xl font-bold mb-2">{{ $supportConversation->title }}</h2>
                <p class="text-blue-100 mb-4">محادثة دعم فني نشطة</p>
                <div class="flex justify-center">
                    <span class="priority-badge priority-{{ $supportConversation->metadata['priority'] ?? 'medium' }}">
                        {{ ucfirst($supportConversation->metadata['priority'] ?? 'medium') }} Priority
                    </span>
                </div>
            </div>
            
            <!-- Chat Interface -->
            <div class="p-6">
                <div class="chat-interface">
                    <div class="chat-messages" id="support-messages">
                        @foreach($supportConversation->messages as $message)
                        <div class="support-message">
                            <div class="{{ $message->sender_id === auth()->id() ? 'message-user' : 'message-support' }}">
                                {{ $message->content }}
                                <div class="text-xs mt-2 opacity-70">
                                    {{ $message->created_at->format('M d, Y H:i') }}
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                        <!-- Typing Indicator -->
                        <div id="typing-indicator" class="typing-animation hidden">
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                            <div class="typing-dot"></div>
                        </div>
                    </div>
                    
                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200">
                        <form id="support-message-form" class="flex space-x-3 space-x-reverse">
                            @csrf
                            <input type="hidden" name="conversation_id" value="{{ $supportConversation->id }}">
                            <div class="flex-1">
                                <textarea name="content" 
                                          id="support-message-input"
                                          class="form-input resize-none"
                                          rows="2"
                                          placeholder="اكتب رسالتك للدعم الفني..."
                                          required></textarea>
                            </div>
                            <button type="submit" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-all flex items-center space-x-2 space-x-reverse">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                                <span>إرسال</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @else
        <!-- New Support Ticket -->
        <div class="support-card">
            <div class="support-header">
                <h2 class="text-3xl font-bold mb-4">كيف يمكننا مساعدتك؟</h2>
                <p class="text-blue-100 text-lg">فريق الدعم الفني جاهز لمساعدتك على مدار الساعة</p>
            </div>
            
            <div class="p-8">
                
                <!-- Enhanced Quick Actions -->
                <div class="quick-actions relative">
                    <!-- Floating Background Particles -->
                    <div class="floating-particles">
                        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
                        <div class="particle" style="left: 30%; animation-delay: 2s;"></div>
                        <div class="particle" style="left: 50%; animation-delay: 4s;"></div>
                        <div class="particle" style="left: 70%; animation-delay: 6s;"></div>
                        <div class="particle" style="left: 90%; animation-delay: 1s;"></div>
                    </div>
                    
                    <div class="quick-action group" onclick="fillSupportForm('مشكلة في الدفع', 'high', 'payment')">
                        <div class="action-icon">💳</div>
                        <h3 class="font-bold text-gray-800 mb-2 text-lg">مشاكل الدفع</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">مشاكل في المعاملات المالية أو الدفع الإلكتروني</p>
                        <div class="mt-4 text-xs text-orange-500 font-semibold uppercase tracking-wide">أولوية عالية</div>
                        
                        <!-- Action Indicator -->
                        <div class="absolute top-4 right-4 w-8 h-8 bg-red-100 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <span class="text-red-600 text-sm">🔥</span>
                        </div>
                    </div>
                    
                    <div class="quick-action group" onclick="fillSupportForm('مشكلة تقنية', 'medium', 'technical')">
                        <div class="action-icon">⚙️</div>
                        <h3 class="font-bold text-gray-800 mb-2 text-lg">مشاكل تقنية</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">أخطاء في النظام أو مشاكل في الأداء والتشغيل</p>
                        <div class="mt-4 text-xs text-yellow-500 font-semibold uppercase tracking-wide">أولوية متوسطة</div>
                        
                        <!-- Action Indicator -->
                        <div class="absolute top-4 right-4 w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <span class="text-yellow-600 text-sm">⚡</span>
                        </div>
                    </div>
                    
                    <div class="quick-action group" onclick="fillSupportForm('استفسار عام', 'low', 'general')">
                        <div class="action-icon">❓</div>
                        <h3 class="font-bold text-gray-800 mb-2 text-lg">استفسار عام</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">أسئلة حول الخدمات أو كيفية الاستخدام</p>
                        <div class="mt-4 text-xs text-green-500 font-semibold uppercase tracking-wide">أولوية منخفضة</div>
                        
                        <!-- Action Indicator -->
                        <div class="absolute top-4 right-4 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <span class="text-green-600 text-sm">💬</span>
                        </div>
                    </div>
                    
                    <div class="quick-action group" onclick="fillSupportForm('طلب ميزة جديدة', 'low', 'feature')">
                        <div class="action-icon">✨</div>
                        <h3 class="font-bold text-gray-800 mb-2 text-lg">طلب ميزة</h3>
                        <p class="text-sm text-gray-600 leading-relaxed">اقتراح ميزات أو تحسينات جديدة للنظام</p>
                        <div class="mt-4 text-xs text-purple-500 font-semibold uppercase tracking-wide">طلب تطوير</div>
                        
                        <!-- Action Indicator -->
                        <div class="absolute top-4 right-4 w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300">
                            <span class="text-purple-600 text-sm">🚀</span>
                        </div>
                    </div>
                </div>

                <!-- Support Form -->
                <form action="{{ route('chat.support.ticket.create') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Subject -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">موضوع التذكرة *</label>
                            <input type="text" 
                                   id="subject"
                                   name="subject" 
                                   class="form-input" 
                                   placeholder="اكتب موضوع المشكلة باختصار"
                                   value="{{ old('subject') }}"
                                   required>
                            @error('subject')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الأولوية *</label>
                            <select id="priority" name="priority" class="form-input" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                            @error('priority')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">فئة المشكلة *</label>
                            <select id="category" name="category" class="form-input" required>
                                <option value="technical" {{ old('category') === 'technical' ? 'selected' : '' }}>مشكلة تقنية</option>
                                <option value="payment" {{ old('category') === 'payment' ? 'selected' : '' }}>مشاكل الدفع</option>
                                <option value="account" {{ old('category') === 'account' ? 'selected' : '' }}>إدارة الحساب</option>
                                <option value="booking" {{ old('category') === 'booking' ? 'selected' : '' }}>الحجوزات</option>
                                <option value="feature" {{ old('category') === 'feature' ? 'selected' : '' }}>طلب ميزة</option>
                                <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>استفسار عام</option>
                                <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>أخرى</option>
                            </select>
                            @error('category')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">وصف المشكلة *</label>
                        <textarea id="description"
                                  name="description" 
                                  class="form-input" 
                                  rows="6" 
                                  placeholder="اشرح المشكلة بالتفصيل، واذكر الخطوات التي قمت بها قبل حدوث المشكلة..."
                                  maxlength="2000"
                                  required>{{ old('description') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">الحد الأقصى: 2000 حرف</p>
                        @error('description')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Enhanced Submit Button -->
                    <div class="text-center relative">
                        <button type="submit" 
                                class="btn-primary-enhanced group inline-flex items-center space-x-3 space-x-reverse">
                            <svg class="w-6 h-6 group-hover:animate-bounce transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span class="text-lg font-bold">إرسال تذكرة الدعم</span>
                            <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center">
                                <span class="text-lg">🎧</span>
                            </div>
                        </button>
                        
                        <!-- Enhanced Help Text -->
                        <div class="mt-6 p-4 bg-gradient-to-r from-orange-50 to-red-50 rounded-2xl border border-orange-200">
                            <p class="text-sm text-orange-700 font-medium mb-2">
                                💡 <strong>نصائح للحصول على دعم أفضل:</strong>
                            </p>
                            <ul class="text-xs text-orange-600 text-right space-y-1">
                                <li>• وضح المشكلة بالتفصيل</li>
                                <li>• أرفق لقطات شاشة إن أمكن</li>
                                <li>• اذكر الخطوات التي قمت بها</li>
                            </ul>
                        </div>
                        
                        <!-- Enhanced Support Stats -->
                        <div class="mt-8 grid grid-cols-3 gap-4">
                            <div class="text-center p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl border border-green-200 hover:shadow-lg transition-all duration-300 group">
                                <div class="text-2xl font-bold text-green-600 group-hover:scale-110 transition-transform duration-300">⚡</div>
                                <div class="text-lg font-bold text-green-700">15min</div>
                                <div class="text-xs text-green-600">متوسط الرد</div>
                            </div>
                            <div class="text-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border border-blue-200 hover:shadow-lg transition-all duration-300 group">
                                <div class="text-2xl font-bold text-blue-600 group-hover:scale-110 transition-transform duration-300">🕒</div>
                                <div class="text-lg font-bold text-blue-700">24/7</div>
                                <div class="text-xs text-blue-600">دعم متواصل</div>
                            </div>
                            <div class="text-center p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl border border-purple-200 hover:shadow-lg transition-all duration-300 group">
                                <div class="text-2xl font-bold text-purple-600 group-hover:scale-110 transition-transform duration-300">❤️</div>
                                <div class="text-lg font-bold text-purple-700">98%</div>
                                <div class="text-xs text-purple-600">رضا العملاء</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- FAQ Section -->
        <div class="support-card mt-8">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">الأسئلة الشائعة</h3>
                
                <!-- Enhanced FAQ Section -->
                <div class="faq-section">
                    <h4 class="text-lg font-bold text-gray-800 mb-6 flex items-center">
                        <div class="w-8 h-8 bg-gradient-to-r from-orange-400 to-red-500 rounded-full flex items-center justify-center mr-3">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        الأسئلة الشائعة
                        <div class="faq-sparkles ml-auto"></div>
                    </h4>
                    
                    <div class="faq-search mb-6 relative">
                        <input type="text" id="faq-search" placeholder="ابحث في الأسئلة الشائعة..." 
                               class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-xl bg-white focus:ring-2 focus:ring-orange-300 focus:border-transparent transition-all duration-300">
                        <div class="absolute right-4 top-1/2 transform -translate-y-1/2">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>

                    <div class="faq-items space-y-4">
                        <div class="faq-item bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300" data-category="account">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300" onclick="toggleFaq(this)">
                                <span class="font-medium text-gray-800">كيف يمكنني إنشاء حساب جديد؟</span>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="category-badge px-2 py-1 bg-blue-100 text-blue-600 text-xs rounded-full">حساب</span>
                                    <svg class="w-5 h-5 transform transition-transform duration-300 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="faq-answer bg-gradient-to-r from-gray-50 to-orange-50 p-5 border-t border-gray-100 hidden">
                                <p class="text-gray-700 leading-relaxed">
                                    انقر على زر "تسجيل جديد" في أعلى الصفحة، ثم أدخل بياناتك الشخصية (الاسم، البريد الإلكتروني، رقم الهاتف). 
                                    ستتلقى رسالة تأكيد على بريدك الإلكتروني لتفعيل حسابك.
                                </p>
                                <div class="mt-3 flex items-center text-sm text-orange-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    نصيحة: تأكد من صحة بريدك الإلكتروني لتتمكن من تفعيل حسابك
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300" data-category="password">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300" onclick="toggleFaq(this)">
                                <span class="font-medium text-gray-800">نسيت كلمة المرور، كيف يمكنني استعادتها؟</span>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="category-badge px-2 py-1 bg-red-100 text-red-600 text-xs rounded-full">أمان</span>
                                    <svg class="w-5 h-5 transform transition-transform duration-300 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="faq-answer bg-gradient-to-r from-gray-50 to-red-50 p-5 border-t border-gray-100 hidden">
                                <p class="text-gray-700 leading-relaxed">
                                    في صفحة تسجيل الدخول، اضغط على "نسيت كلمة المرور؟" وأدخل بريدك الإلكتروني. 
                                    ستتلقى رابط آمن لإعادة تعيين كلمة المرور خلال دقائق.
                                </p>
                                <div class="mt-3 flex items-center text-sm text-red-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    مهم: تحقق من صندوق الرسائل المرفوضة إذا لم تجد الرسالة
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300" data-category="payment">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300" onclick="toggleFaq(this)">
                                <span class="font-medium text-gray-800">ما هي طرق الدفع المتاحة؟</span>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="category-badge px-2 py-1 bg-green-100 text-green-600 text-xs rounded-full">دفع</span>
                                    <svg class="w-5 h-5 transform transition-transform duration-300 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="faq-answer bg-gradient-to-r from-gray-50 to-green-50 p-5 border-t border-gray-100 hidden">
                                <p class="text-gray-700 leading-relaxed mb-3">
                                    نقبل جميع الطرق التالية للدفع الآمن:
                                </p>
                                <div class="grid grid-cols-2 gap-3 mb-3">
                                    <div class="flex items-center p-2 bg-white rounded-lg">
                                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                        البطاقات الائتمانية
                                    </div>
                                    <div class="flex items-center p-2 bg-white rounded-lg">
                                        <svg class="w-5 h-5 text-purple-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        مدى
                                    </div>
                                    <div class="flex items-center p-2 bg-white rounded-lg">
                                        <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                        </svg>
                                        التحويل البنكي
                                    </div>
                                    <div class="flex items-center p-2 bg-white rounded-lg">
                                        <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        المحافظ الرقمية
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center text-sm text-green-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                    </svg>
                                    جميع المعاملات آمنة ومشفرة بأحدث التقنيات
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300" data-category="support">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300" onclick="toggleFaq(this)">
                                <span class="font-medium text-gray-800">كم يستغرق الرد على استفساراتي؟</span>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="category-badge px-2 py-1 bg-purple-100 text-purple-600 text-xs rounded-full">دعم</span>
                                    <svg class="w-5 h-5 transform transition-transform duration-300 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="faq-answer bg-gradient-to-r from-gray-50 to-purple-50 p-5 border-t border-gray-100 hidden">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <div class="text-center p-3 bg-white rounded-lg border border-purple-100">
                                        <div class="text-2xl font-bold text-purple-600">15 دقيقة</div>
                                        <div class="text-sm text-gray-600">المشاكل العاجلة</div>
                                    </div>
                                    <div class="text-center p-3 bg-white rounded-lg border border-blue-100">
                                        <div class="text-2xl font-bold text-blue-600">4 ساعات</div>
                                        <div class="text-sm text-gray-600">الاستفسارات العادية</div>
                                    </div>
                                    <div class="text-center p-3 bg-white rounded-lg border border-green-100">
                                        <div class="text-2xl font-bold text-green-600">24/7</div>
                                        <div class="text-sm text-gray-600">متاح دائماً</div>
                                    </div>
                                </div>
                                <div class="mt-3 flex items-center text-sm text-purple-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    فريق دعم متخصص متاح على مدار الساعة لخدمتك
                                </div>
                            </div>
                        </div>
                        
                        <div class="faq-item bg-white rounded-xl border border-gray-100 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300" data-category="tickets">
                            <div class="faq-question cursor-pointer p-5 flex justify-between items-center hover:bg-gradient-to-r hover:from-orange-50 hover:to-red-50 transition-all duration-300" onclick="toggleFaq(this)">
                                <span class="font-medium text-gray-800">كيف يمكنني إلغاء أو تعديل حجز التذاكر؟</span>
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <span class="category-badge px-2 py-1 bg-orange-100 text-orange-600 text-xs rounded-full">تذاكر</span>
                                    <svg class="w-5 h-5 transform transition-transform duration-300 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>
                            </div>
                            <div class="faq-answer bg-gradient-to-r from-gray-50 to-orange-50 p-5 border-t border-gray-100 hidden">
                                <p class="text-gray-700 leading-relaxed mb-3">
                                    يمكنك إدارة حجوزاتك بسهولة من خلال لوحة التحكم الخاصة بك:
                                </p>
                                <div class="space-y-2 mb-3">
                                    <div class="flex items-center p-2 bg-white rounded-lg">
                                        <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">1</span>
                                        ادخل إلى "حجوزاتي" من القائمة الرئيسية
                                    </div>
                                    <div class="flex items-center p-2 bg-white rounded-lg">
                                        <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">2</span>
                                        اختر الحجز المراد تعديله أو إلغاؤه
                                    </div>
                                    <div class="flex items-center p-2 bg-white rounded-lg">
                                        <span class="w-6 h-6 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-bold mr-3">3</span>
                                        اضغط على "تعديل" أو "إلغاء" حسب حاجتك
                                    </div>
                                </div>
                                <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex items-center text-sm text-yellow-700">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.98-.833-2.75 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                        </svg>
                                        <strong>تنبيه:</strong> سياسة الإلغاء تختلف حسب نوع الفعالية وتوقيت الإلغاء
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="faq-no-results hidden text-center py-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <h4 class="text-lg font-medium text-gray-700 mb-2">لا توجد نتائج</h4>
                        <p class="text-gray-500">جرب البحث بكلمات مختلفة أو تواصل معنا مباشرة</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="mt-8 text-center">
            <div class="support-card p-6">
                <h4 class="text-lg font-bold text-gray-800 mb-4">طرق التواصل الأخرى</h4>
                <div class="flex justify-center space-x-8 space-x-reverse">
                    <div class="text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">الهاتف</p>
                        <p class="text-xs text-gray-600" dir="ltr">+966 50 123 4567</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">البريد الإلكتروني</p>
                        <p class="text-xs text-gray-600">support@ticketsfbr.com</p>
                    </div>
                    
                    <div class="text-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium">ساعات العمل</p>
                        <p class="text-xs text-gray-600">24/7 متاح دائماً</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/creative-interactions.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎧 Enhanced Support System - Loading...');
    
    // Initialize enhanced support features
    initializeSupportAnimations();
    initializeFormEnhancements();
    initializeQuickActions();
    initializeFAQSystem();
    
    console.log('🎧 Enhanced Support System - Ready! ✨');
});

function initializeSupportAnimations() {
    // Add floating animation to quick actions
    const quickActions = document.querySelectorAll('.quick-action');
    quickActions.forEach((action, index) => {
        action.style.animationDelay = `${index * 0.2}s`;
        action.classList.add('animate-fade-in-up');
        
        // Add click animation
        action.addEventListener('click', function() {
            this.style.animation = 'clickPulse 0.3s ease-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 300);
        });
    });
    
    // Add sparkle effect to support stats
    const stats = document.querySelectorAll('.text-center.p-4');
    stats.forEach(stat => {
        stat.addEventListener('mouseenter', function() {
            createSparkleEffect(this);
        });
    });
    
    // Add floating animation to contact info
    const contactCards = document.querySelectorAll('.text-center > div');
    contactCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.3}s`;
        card.classList.add('animate-fade-in-up');
    });
}

function initializeFormEnhancements() {
    const form = document.querySelector('form[action*="support"]');
    if (!form) return;
    
    // Enhanced form validation
    const inputs = form.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('form-field-focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('form-field-focused');
            if (this.value.trim()) {
                this.parentElement.classList.add('form-field-filled');
            } else {
                this.parentElement.classList.remove('form-field-filled');
            }
        });
        
        // Real-time validation feedback
        input.addEventListener('input', function() {
            validateField(this);
        });
    });
    
    // Enhanced character counter for description
    const descriptionField = document.getElementById('description');
    if (descriptionField) {
        const maxLength = 2000;
        const counter = createCharacterCounter(descriptionField, maxLength);
        descriptionField.parentElement.appendChild(counter);
        
        descriptionField.addEventListener('input', function() {
            updateCharacterCounter(this, counter, maxLength);
        });
    }
    
    // Enhanced form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        submitSupportForm(this);
    });
}

function initializeQuickActions() {
    // Enhanced quick action functionality with better animations
    window.fillSupportForm = function(subject, priority, category) {
        const subjectField = document.getElementById('subject');
        const priorityField = document.getElementById('priority');
        const categoryField = document.getElementById('category');
        
        if (subjectField) {
            subjectField.value = subject;
            subjectField.style.animation = 'fieldFill 0.5s ease-out';
            subjectField.parentElement.classList.add('form-field-filled');
            subjectField.focus();
        }
        
        if (priorityField) {
            priorityField.value = priority;
            priorityField.style.animation = 'fieldFill 0.5s ease-out 0.1s both';
            priorityField.parentElement.classList.add('form-field-filled');
        }
        
        if (categoryField) {
            categoryField.value = category;
            categoryField.style.animation = 'fieldFill 0.5s ease-out 0.2s both';
            categoryField.parentElement.classList.add('form-field-filled');
        }
        
        // Scroll to form with enhanced animation
        const form = document.querySelector('form[action*="support"]');
        if (form) {
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        
        // Show notification
        showNotification(`تم تعبئة النموذج لـ "${subject}" 📝`, 'success');
        
        // Add visual feedback
        const clickedAction = event.currentTarget;
        clickedAction.style.transform = 'scale(0.95)';
        setTimeout(() => {
            clickedAction.style.transform = '';
        }, 150);
    };
}

function initializeFAQSystem() {
    // Enhanced FAQ toggle functionality
    window.toggleFaq = function(element) {
        const faqItem = element.parentElement;
        const isActive = faqItem.classList.contains('active');
        const arrow = element.querySelector('svg');
        const answer = faqItem.querySelector('.faq-answer');
        
        // Close all FAQ items with animation
        document.querySelectorAll('.faq-item').forEach(item => {
            item.classList.remove('active');
            const itemArrow = item.querySelector('svg');
            const itemAnswer = item.querySelector('.faq-answer');
            if (itemArrow) {
                itemArrow.style.transform = 'rotate(0deg)';
            }
            if (itemAnswer) {
                itemAnswer.classList.add('hidden');
            }
        });
        
        // Open clicked item if it wasn't active
        if (!isActive) {
            faqItem.classList.add('active');
            if (arrow) {
                arrow.style.transform = 'rotate(180deg)';
            }
            if (answer) {
                answer.classList.remove('hidden');
            }
            
            // Add sparkle effect
            createSparkleEffect(element);
            
            // Show notification for helpful FAQ
            const questionText = element.querySelector('span').textContent;
            showNotification(`تم فتح: ${questionText} 💡`, 'info');
        }
    };
    
    // FAQ Search functionality
    const faqSearch = document.getElementById('faq-search');
    if (faqSearch) {
        faqSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            const faqItems = document.querySelectorAll('.faq-item');
            const noResults = document.querySelector('.faq-no-results');
            let visibleCount = 0;
            
            faqItems.forEach(item => {
                const question = item.querySelector('.faq-question span').textContent.toLowerCase();
                const answer = item.querySelector('.faq-answer').textContent.toLowerCase();
                const category = item.dataset.category || '';
                
                const matches = question.includes(searchTerm) || 
                               answer.includes(searchTerm) || 
                               category.includes(searchTerm);
                
                if (matches || searchTerm === '') {
                    item.style.display = 'block';
                    item.style.animation = 'fadeIn 0.3s ease-out';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (visibleCount === 0 && searchTerm !== '') {
                noResults.classList.remove('hidden');
                noResults.style.animation = 'fadeIn 0.3s ease-out';
            } else {
                noResults.classList.add('hidden');
            }
            
            // Highlight search terms
            if (searchTerm) {
                highlightSearchTerms(searchTerm);
            } else {
                clearHighlights();
            }
        });
    }
}

function highlightSearchTerms(searchTerm) {
    const faqItems = document.querySelectorAll('.faq-item[style*="block"]');
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question span');
        const answer = item.querySelector('.faq-answer p, .faq-answer');
        
        [question, answer].forEach(element => {
            if (element && element.textContent) {
                const originalText = element.textContent;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                const highlightedText = originalText.replace(regex, '<mark class="bg-yellow-200 text-yellow-800 px-1 rounded">$1</mark>');
                
                if (highlightedText !== originalText) {
                    element.innerHTML = highlightedText;
                }
            }
        });
    });
}

function clearHighlights() {
    const highlights = document.querySelectorAll('.faq-item mark');
    highlights.forEach(mark => {
        const parent = mark.parentNode;
        parent.replaceChild(document.createTextNode(mark.textContent), mark);
        parent.normalize();
    });
}

function createCharacterCounter(field, maxLength) {
    const counter = document.createElement('div');
    counter.className = 'character-counter text-xs text-gray-500 mt-2 flex justify-between items-center';
    counter.innerHTML = `
        <span class="count-text">0 / ${maxLength} حرف</span>
        <div class="count-bar w-24 h-1 bg-gray-200 rounded-full overflow-hidden">
            <div class="count-progress h-full bg-orange-400 transition-all duration-300" style="width: 0%"></div>
        </div>
    `;
    return counter;
}

function updateCharacterCounter(field, counter, maxLength) {
    const currentLength = field.value.length;
    const percentage = (currentLength / maxLength) * 100;
    
    const countText = counter.querySelector('.count-text');
    const progressBar = counter.querySelector('.count-progress');
    
    countText.textContent = `${currentLength} / ${maxLength} حرف`;
    progressBar.style.width = `${Math.min(percentage, 100)}%`;
    
    // Color coding with animations
    if (percentage > 90) {
        progressBar.style.backgroundColor = '#ef4444';
        countText.style.color = '#ef4444';
        if (percentage > 95) {
            progressBar.style.animation = 'pulse 1s infinite';
        }
    } else if (percentage > 75) {
        progressBar.style.backgroundColor = '#f59e0b';
        countText.style.color = '#f59e0b';
        progressBar.style.animation = '';
    } else {
        progressBar.style.backgroundColor = '#f97316';
        countText.style.color = '#6b7280';
        progressBar.style.animation = '';
    }
    
    // Prevent overflow
    if (currentLength > maxLength) {
        field.value = field.value.substring(0, maxLength);
    }
}

function validateField(field) {
    const value = field.value.trim();
    const fieldContainer = field.parentElement;
    
    // Remove previous validation classes
    fieldContainer.classList.remove('field-valid', 'field-invalid');
    
    // Basic validation
    let isValid = true;
    
    if (field.hasAttribute('required') && !value) {
        isValid = false;
    }
    
    if (field.type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
    }
    
    // Add validation classes with animations
    if (value && isValid) {
        fieldContainer.classList.add('field-valid');
    } else if (value && !isValid) {
        fieldContainer.classList.add('field-invalid');
        field.style.animation = 'shake 0.5s ease-in-out';
        setTimeout(() => {
            field.style.animation = '';
        }, 500);
    }
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

async function submitSupportForm(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const originalContent = submitButton.innerHTML;
    
    // Show loading state with enhanced animation
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <div class="flex items-center justify-center space-x-2 space-x-reverse">
            <div class="w-6 h-6 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
            <span>جاري الإرسال...</span>
        </div>
    `;
    
    try {
        const formData = new FormData(form);
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            // Success animation
            submitButton.innerHTML = `
                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>تم الإرسال بنجاح!</span>
                </div>
            `;
            
            // Show success notification
            showNotification('تم إرسال تذكرة الدعم بنجاح! 🎉', 'success');
            
            // Add confetti effect
            createConfettiEffect();
            
            // Redirect after delay
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error('فشل في إرسال التذكرة');
        }
    } catch (error) {
        console.error('Error submitting form:', error);
        
        // Error animation
        submitButton.innerHTML = `
            <div class="flex items-center justify-center space-x-2 space-x-reverse">
                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                <span>حدث خطأ - حاول مرة أخرى</span>
            </div>
        `;
        
        showNotification('حدث خطأ في إرسال التذكرة', 'error');
        
        // Restore original state after delay
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.innerHTML = originalContent;
        }, 3000);
    }
}

@if($supportConversation)
// Enhanced support conversation functionality
document.addEventListener('DOMContentLoaded', function() {
    const supportMessageForm = document.getElementById('support-message-form');
    const typingIndicator = document.getElementById('typing-indicator');
    
    if (supportMessageForm) {
        supportMessageForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageInput = document.getElementById('support-message-input');
            const message = messageInput.value.trim();
            
            if (!message) return;
            
            // Show typing indicator with animation
            typingIndicator.classList.remove('hidden');
            typingIndicator.style.animation = 'fadeIn 0.3s ease-out';
            
            // Add message to chat immediately
            addMessageToChat(message, true);
            
            try {
                const response = await fetch(`/chat/conversations/{{ $supportConversation->id }}/messages`, {
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
                    
                    // Simulate support response with enhanced animation
                    setTimeout(() => {
                        typingIndicator.classList.add('hidden');
                        addMessageToChat('شكراً لك على رسالتك. سيقوم أحد مختصي الدعم الفني بالرد عليك خلال 15 دقيقة. 🎧', false);
                        showNotification('تم إرسال رسالتك بنجاح! 📩', 'success');
                    }, 2000);
                }
            } catch (error) {
                console.error('Error sending message:', error);
                typingIndicator.classList.add('hidden');
                showNotification('حدث خطأ في إرسال الرسالة', 'error');
            }
        });
    }
});

function addMessageToChat(content, isUser) {
    const messagesContainer = document.getElementById('support-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'support-message';
    messageDiv.style.opacity = '0';
    messageDiv.style.animation = 'messageSlideIn 0.5s ease-out forwards';
    
    messageDiv.innerHTML = `
        <div class="${isUser ? 'message-user' : 'message-support'} relative group">
            <div class="whitespace-pre-wrap">${content}</div>
            <div class="text-xs mt-2 opacity-70 flex items-center justify-between">
                <span>${new Date().toLocaleTimeString('ar-SA', {hour: '2-digit', minute: '2-digit'})}</span>
                ${isUser ? '<div class="text-green-300"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg></div>' : ''}
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTo({
        top: messagesContainer.scrollHeight,
        behavior: 'smooth'
    });
}
@endif

function createSparkleEffect(element) {
    for (let i = 0; i < 5; i++) {
        setTimeout(() => {
            const sparkle = document.createElement('div');
            sparkle.innerHTML = '✨';
            sparkle.style.position = 'absolute';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.userSelect = 'none';
            sparkle.style.fontSize = Math.random() * 8 + 12 + 'px';
            sparkle.style.left = Math.random() * 100 + '%';
            sparkle.style.top = Math.random() * 100 + '%';
            sparkle.style.animation = 'sparkleFloat 1s ease-out forwards';
            sparkle.style.zIndex = '1000';
            
            element.style.position = 'relative';
            element.appendChild(sparkle);
            
            setTimeout(() => sparkle.remove(), 1000);
        }, i * 100);
    }
}

function createConfettiEffect() {
    const colors = ['#f97316', '#ea580c', '#dc2626', '#059669', '#3b82f6', '#8b5cf6'];
    
    for (let i = 0; i < 30; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            confetti.style.position = 'fixed';
            confetti.style.width = '10px';
            confetti.style.height = '10px';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.top = '-10px';
            confetti.style.borderRadius = '50%';
            confetti.style.pointerEvents = 'none';
            confetti.style.zIndex = '9999';
            confetti.style.animation = `confettiFall ${Math.random() * 3 + 2}s linear forwards`;
            
            document.body.appendChild(confetti);
            
            setTimeout(() => confetti.remove(), 5000);
        }, i * 50);
    }
}

// Additional CSS animations
const additionalStyles = document.createElement('style');
additionalStyles.textContent = `
    @keyframes clickPulse {
        0% { transform: scale(1); }
        50% { transform: scale(0.95); }
        100% { transform: scale(1); }
    }
    
    @keyframes fieldFill {
        0% { background-color: transparent; }
        50% { background-color: rgba(249, 115, 22, 0.1); }
        100% { background-color: transparent; }
    }
    
    @keyframes confettiFall {
        0% { transform: translateY(-10px) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    @keyframes messageSlideIn {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes fadeIn {
        0% { opacity: 0; }
        100% { opacity: 1; }
    }
    
    .form-field-focused {
        transform: scale(1.02);
        box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1);
        transition: all 0.3s ease;
    }
    
    .form-field-filled .form-input {
        border-color: #10b981;
        background-color: rgba(16, 185, 129, 0.05);
    }
    
    .field-valid .form-input {
        border-color: #10b981;
        background-color: rgba(16, 185, 129, 0.05);
    }
    
    .field-invalid .form-input {
        border-color: #ef4444;
        background-color: rgba(239, 68, 68, 0.05);
    }
    
    .quick-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .faq-item.active .faq-answer {
        animation: fadeIn 0.3s ease-out;
    }
`;
document.head.appendChild(additionalStyles);

console.log('🎧 Enhanced Support System - All features loaded! ✨');
</script>
@endpush
@endsection
