<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\Booking;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EnhancedBookingForm extends Component
{
    public Service $service;
    public $step = 1;
    public $maxSteps = 4;
    
    // Step 1: Service Details
    public $selectedDate;
    public $selectedTime;
    public $guestCount = 1;
    public $specialRequests = '';
    
    // Step 2: Customer Information
    public $customerName = '';
    public $customerEmail = '';
    public $customerPhone = '';
    public $customerAddress = '';
    
    // Step 3: Payment Gateway Selection
    public $selectedGateway = null;
    public $paymentGateways = [];
    
    // Step 4: Confirmation
    public $booking = null;
    public $totalAmount = 0;
    public $processingPayment = false;

    protected $rules = [
        'selectedDate' => 'required|date|after:today',
        'selectedTime' => 'required',
        'guestCount' => 'required|integer|min:1|max:1000',
        'customerName' => 'required|string|min:2|max:100',
        'customerEmail' => 'required|email',
        'customerPhone' => 'required|regex:/^[0-9+\-\s()]+$/|min:10',
        'customerAddress' => 'required|string|min:10|max:500',
        'selectedGateway' => 'required|exists:payment_gateways,id',
    ];

    protected $messages = [
        'selectedDate.required' => 'يرجى اختيار تاريخ الحجز',
        'selectedDate.after' => 'يجب أن يكون تاريخ الحجز في المستقبل',
        'selectedTime.required' => 'يرجى اختيار وقت الحجز',
        'guestCount.required' => 'يرجى تحديد عدد الضيوف',
        'guestCount.min' => 'يجب أن يكون عدد الضيوف أكثر من صفر',
        'customerName.required' => 'يرجى إدخال الاسم الكامل',
        'customerEmail.required' => 'يرجى إدخال البريد الإلكتروني',
        'customerEmail.email' => 'يرجى إدخال بريد إلكتروني صحيح',
        'customerPhone.required' => 'يرجى إدخال رقم الهاتف',
        'customerAddress.required' => 'يرجى إدخال العنوان',
        'selectedGateway.required' => 'يرجى اختيار وسيلة الدفع',
    ];

    public function mount(Service $service)
    {
        $this->service = $service;
        $this->calculateTotal();
        $this->loadPaymentGateways();
        
        // Pre-fill user data if authenticated
        if (Auth::check()) {
            $user = Auth::user();
            $this->customerName = $user->name;
            $this->customerEmail = $user->email;
        }
    }

    public function loadPaymentGateways()
    {
        $this->paymentGateways = PaymentGateway::active()
            ->where(function ($query) {
                $query->whereDoesntHave('merchantSettings')
                      ->orWhereHas('merchantSettings', function ($subQuery) {
                          $subQuery->where('merchant_id', $this->service->user_id)
                                   ->where('is_enabled', true);
                      });
            })
            ->get();
    }

    public function calculateTotal()
    {
        $basePrice = $this->service->price * $this->guestCount;
        
        // Add any additional fees based on guest count or date
        $additionalFees = 0;
        if ($this->guestCount > 100) {
            $additionalFees += 500; // Large event fee
        }
        
        $this->totalAmount = $basePrice + $additionalFees;
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        
        if ($this->step < $this->maxSteps) {
            $this->step++;
        }
        
        if ($this->step == 3) {
            $this->calculateTotal();
        }
    }

    public function previousStep()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function validateCurrentStep()
    {
        switch ($this->step) {
            case 1:
                $this->validate([
                    'selectedDate' => $this->rules['selectedDate'],
                    'selectedTime' => $this->rules['selectedTime'],
                    'guestCount' => $this->rules['guestCount'],
                ]);
                break;
            case 2:
                $this->validate([
                    'customerName' => $this->rules['customerName'],
                    'customerEmail' => $this->rules['customerEmail'],
                    'customerPhone' => $this->rules['customerPhone'],
                    'customerAddress' => $this->rules['customerAddress'],
                ]);
                break;
            case 3:
                $this->validate([
                    'selectedGateway' => $this->rules['selectedGateway'],
                ]);
                break;
        }
    }

    public function processBooking()
    {
        $this->processingPayment = true;
        
        try {
            $this->validateCurrentStep();
            
            // Create booking
            $this->booking = Booking::create([
                'service_id' => $this->service->id,
                'user_id' => Auth::id(),
                'merchant_id' => $this->service->user_id,
                'booking_date' => $this->selectedDate,
                'booking_time' => $this->selectedTime,
                'guest_count' => $this->guestCount,
                'total_amount' => $this->totalAmount,
                'status' => 'pending',
                'special_requests' => $this->specialRequests,
                'customer_name' => $this->customerName,
                'customer_email' => $this->customerEmail,
                'customer_phone' => $this->customerPhone,
                'customer_address' => $this->customerAddress,
                'booking_number' => 'BK-' . strtoupper(uniqid()),
            ]);

            // Initialize payment
            $paymentService = new PaymentService();
            $gateway = PaymentGateway::find($this->selectedGateway);
            
            $paymentResult = $paymentService->createPayment(
                $this->booking,
                $gateway,
                $this->totalAmount
            );

            if ($paymentResult['success']) {
                $this->step = 4;
                $this->dispatch('booking-created', [
                    'booking_id' => $this->booking->id,
                    'booking_number' => $this->booking->booking_number
                ]);
            } else {
                $this->addError('payment', 'حدث خطأ في معالجة الدفع: ' . $paymentResult['message']);
            }
            
        } catch (\Exception $e) {
            $this->addError('general', 'حدث خطأ غير متوقع. يرجى المحاولة مرة أخرى.');
            \Log::error('Booking Error: ' . $e->getMessage());
        } finally {
            $this->processingPayment = false;
        }
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'guestCount') {
            $this->calculateTotal();
        }
    }

    public function getAvailableTimesProperty()
    {
        return [
            '09:00', '10:00', '11:00', '12:00',
            '13:00', '14:00', '15:00', '16:00',
            '17:00', '18:00', '19:00', '20:00'
        ];
    }

    public function render()
    {
        return view('livewire.enhanced-booking-form');
    }
}
