@extends('layouts.app')

@section('title', 'الدعم الفني - Technical Support')
@section('description', 'تواصل مع فريق الدعم الفني للحصول على المساعدة')

@push('styles')
<style>
    .support-container {
        min-height: calc(100vh - 120px);
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }
    
    .support-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
    }
    
    .support-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 2rem;
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
        background: radial-gradient(circle, rgba(255,255,255,0.1) 20%, transparent 50%);
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }
    
    .priority-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
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
                
                <!-- Quick Actions -->
                <div class="quick-actions">
                    <div class="quick-action" onclick="fillSupportForm('مشكلة في الدفع', 'high', 'payment')">
                        <div class="text-4xl mb-3">💳</div>
                        <h3 class="font-bold text-gray-800 mb-2">مشاكل الدفع</h3>
                        <p class="text-sm text-gray-600">مشاكل في المعاملات المالية أو الدفع</p>
                    </div>
                    
                    <div class="quick-action" onclick="fillSupportForm('مشكلة تقنية', 'medium', 'technical')">
                        <div class="text-4xl mb-3">⚙️</div>
                        <h3 class="font-bold text-gray-800 mb-2">مشاكل تقنية</h3>
                        <p class="text-sm text-gray-600">أخطاء في النظام أو مشاكل في الأداء</p>
                    </div>
                    
                    <div class="quick-action" onclick="fillSupportForm('استفسار عام', 'low', 'general')">
                        <div class="text-4xl mb-3">❓</div>
                        <h3 class="font-bold text-gray-800 mb-2">استفسار عام</h3>
                        <p class="text-sm text-gray-600">أسئلة حول الخدمات أو كيفية الاستخدام</p>
                    </div>
                    
                    <div class="quick-action" onclick="fillSupportForm('طلب ميزة جديدة', 'low', 'feature')">
                        <div class="text-4xl mb-3">✨</div>
                        <h3 class="font-bold text-gray-800 mb-2">طلب ميزة</h3>
                        <p class="text-sm text-gray-600">اقتراح ميزات أو تحسينات جديدة</p>
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

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" 
                                class="bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-bold py-4 px-8 rounded-xl transition-all transform hover:scale-105 flex items-center space-x-3 space-x-reverse mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                            <span>إرسال تذكرة الدعم</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- FAQ Section -->
        <div class="support-card mt-8">
            <div class="p-8">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">الأسئلة الشائعة</h3>
                
                <div class="space-y-4">
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>كيف يمكنني إنشاء حساب جديد؟</span>
                            <svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            يمكنك إنشاء حساب جديد من خلال الضغط على "تسجيل" في أعلى الصفحة، ثم إدخال بياناتك الأساسية والتحقق من البريد الإلكتروني.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>كيف يمكنني استرداد كلمة المرور؟</span>
                            <svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            في صفحة تسجيل الدخول، اضغط على "نسيت كلمة المرور؟" وأدخل بريدك الإلكتروني لتلقي رابط إعادة تعيين كلمة المرور.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>ما هي طرق الدفع المتاحة؟</span>
                            <svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            نقبل البطاقات الائتمانية (فيزا، ماستركارد)، مدى، والتحويل البنكي المباشر. جميع المعاملات آمنة ومشفرة.
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question" onclick="toggleFaq(this)">
                            <span>كم يستغرق الرد على استفساراتي؟</span>
                            <svg class="w-5 h-5 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                        <div class="faq-answer">
                            نهدف للرد خلال 24 ساعة للاستفسارات العادية، و4 ساعات للمشاكل العاجلة. فريق الدعم متاح على مدار الساعة.
                        </div>
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
<script>
function fillSupportForm(subject, priority, category) {
    document.getElementById('subject').value = subject;
    document.getElementById('priority').value = priority;
    document.getElementById('category').value = category;
    
    // Scroll to form
    document.getElementById('subject').scrollIntoView({ behavior: 'smooth' });
    document.getElementById('subject').focus();
}

function toggleFaq(element) {
    const faqItem = element.parentElement;
    const isActive = faqItem.classList.contains('active');
    
    // Close all FAQ items
    document.querySelectorAll('.faq-item').forEach(item => {
        item.classList.remove('active');
        item.querySelector('svg').style.transform = 'rotate(0deg)';
    });
    
    // Open clicked item if it wasn't active
    if (!isActive) {
        faqItem.classList.add('active');
        element.querySelector('svg').style.transform = 'rotate(180deg)';
    }
}

@if($supportConversation)
// Handle support message form
document.getElementById('support-message-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const messageInput = document.getElementById('support-message-input');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    // Show typing indicator
    const typingIndicator = document.getElementById('typing-indicator');
    typingIndicator.classList.remove('hidden');
    
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
            // Add message to chat
            addMessageToChat(message, true);
            
            // Simulate support response (in real app, this would come via WebSocket)
            setTimeout(() => {
                typingIndicator.classList.add('hidden');
                addMessageToChat('شكراً لك على رسالتك. سيقوم أحد مختصي الدعم الفني بالرد عليك قريباً.', false);
            }, 2000);
        }
    } catch (error) {
        console.error('Error sending message:', error);
        typingIndicator.classList.add('hidden');
    }
});

function addMessageToChat(content, isUser) {
    const messagesContainer = document.getElementById('support-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'support-message';
    
    messageDiv.innerHTML = `
        <div class="${isUser ? 'message-user' : 'message-support'}">
            ${content}
            <div class="text-xs mt-2 opacity-70">
                ${new Date().toLocaleString('ar-SA')}
            </div>
        </div>
    `;
    
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}
@endif

// Character counter for description
document.getElementById('description').addEventListener('input', function(e) {
    const maxLength = 2000;
    const currentLength = e.target.value.length;
    const remaining = maxLength - currentLength;
    
    // You can add a character counter display here if needed
    if (remaining < 0) {
        e.target.value = e.target.value.substring(0, maxLength);
    }
});
</script>
@endpush
@endsection
