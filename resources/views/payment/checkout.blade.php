@extends('layouts.public')

@section('title', 'الدفع - ' . $booking->service->name)

@section('content')
<div class="min-h-screen bg-slate-50 py-8" dir="rtl">
    <div class="container mx-auto px-4">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 space-x-reverse text-sm text-slate-600 mb-8">
            <a href="{{ route('merchant.booking', $booking->merchant_id) }}" class="hover:text-primary">
                {{ $booking->merchant->business_name }}
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <a href="{{ route('merchant.service.booking', [$booking->merchant_id, $booking->service_id]) }}" class="hover:text-primary">
                {{ $booking->service->name }}
            </a>
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-primary font-medium">الدفع</span>
        </nav>

        <!-- Payment Component -->
        <livewire:payment-checkout :booking="$booking" />
    </div>
</div>

@push('styles')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #f9682e 0%, #ff7842 100%);
    }
    
    .card-hover {
        transition: all 0.3s ease-in-out;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script>
    // Payment form enhancements
    document.addEventListener('DOMContentLoaded', function() {
        // Format card number input
        const cardNumberInput = document.getElementById('cardNumber');
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                e.target.value = value;
            });
        }

        // Format expiry date input
        const expiryInput = document.getElementById('expiryDate');
        if (expiryInput) {
            expiryInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length >= 2) {
                    value = value.substring(0, 2) + '/' + value.substring(2, 4);
                }
                e.target.value = value;
            });
        }

        // CVV input - numbers only
        const cvvInput = document.getElementById('cvv');
        if (cvvInput) {
            cvvInput.addEventListener('input', function(e) {
                e.target.value = e.target.value.replace(/\D/g, '');
            });
        }
    });
</script>
@endpush
@endsection
