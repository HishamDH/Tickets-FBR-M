<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Booking;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Auth;

class PaymentCheckout extends Component
{
    public Booking $booking;
    public $selectedGateway = null;
    public $availableGateways = [];
    public $paymentData = [];
    public $step = 1; // 1: اختيار البوابة, 2: إدخال البيانات, 3: المعالجة
    public $isProcessing = false;
    public $paymentResult = null;

    // بيانات الدفع
    public $cardNumber = '';
    public $expiryDate = '';
    public $cvv = '';
    public $cardHolder = '';
    public $saveCard = false;

    protected $rules = [
        'selectedGateway' => 'required|exists:payment_gateways,id',
        'cardNumber' => 'required_if:selectedGateway,1,2|min:16',
        'expiryDate' => 'required_if:selectedGateway,1,2',
        'cvv' => 'required_if:selectedGateway,1,2|min:3',
        'cardHolder' => 'required_if:selectedGateway,1,2|min:3',
    ];

    protected $messages = [
        'selectedGateway.required' => 'يرجى اختيار طريقة الدفع',
        'cardNumber.required_if' => 'رقم البطاقة مطلوب',
        'cardNumber.min' => 'رقم البطاقة يجب أن يكون 16 رقم على الأقل',
        'expiryDate.required_if' => 'تاريخ انتهاء البطاقة مطلوب',
        'cvv.required_if' => 'رمز الأمان مطلوب',
        'cvv.min' => 'رمز الأمان يجب أن يكون 3 أرقام على الأقل',
        'cardHolder.required_if' => 'اسم حامل البطاقة مطلوب',
        'cardHolder.min' => 'اسم حامل البطاقة قصير جداً',
    ];

    public function mount(Booking $booking)
    {
        $this->booking = $booking;
        $this->loadAvailableGateways();
    }

    public function loadAvailableGateways()
    {
        $paymentService = app(PaymentService::class);
        $this->availableGateways = $paymentService->getAvailableGateways($this->booking->merchant_id);
    }

    public function selectGateway($gatewayId)
    {
        $this->selectedGateway = $gatewayId;
        $this->step = 2;
        $this->resetValidation();
    }

    public function goBack()
    {
        if ($this->step > 1) {
            $this->step--;
        }
    }

    public function processPayment()
    {
        $this->validate();
        
        $this->isProcessing = true;
        $this->step = 3;

        try {
            $paymentService = app(PaymentService::class);
            $gateway = PaymentGateway::findOrFail($this->selectedGateway);
            
            // إنشاء سجل الدفع
            $payment = $paymentService->createPayment($this->booking, $gateway, [
                'payment_method' => $gateway->code,
                'customer_ip' => request()->ip(),
            ]);

            // معالجة الدفع
            $paymentData = $this->preparePaymentData();
            $result = $paymentService->processPayment($payment, $paymentData);

            $this->paymentResult = $result;

            if ($result['success']) {
                session()->flash('success', 'تم الدفع بنجاح');
                return redirect()->route('merchant.booking.confirmation', [
                    'merchant' => $this->booking->merchant_id,
                    'booking' => $this->booking->id,
                ]);
            } else {
                $this->addError('payment', $result['message'] ?? 'حدث خطأ أثناء معالجة الدفع');
            }

        } catch (\Exception $e) {
            $this->addError('payment', 'حدث خطأ غير متوقع: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    protected function preparePaymentData(): array
    {
        $gateway = PaymentGateway::find($this->selectedGateway);
        
        $data = [
            'payment_method' => $gateway->code,
        ];

        // إضافة بيانات البطاقة للبوابات التي تحتاجها
        if (in_array($gateway->code, ['visa', 'mastercard', 'apple_pay'])) {
            $data = array_merge($data, [
                'card_number' => $this->cardNumber,
                'expiry_date' => $this->expiryDate,
                'cvv' => $this->cvv,
                'card_holder' => $this->cardHolder,
                'save_card' => $this->saveCard,
            ]);
        }

        // للاختبار في البيئة المحلية
        if (config('app.env') === 'local') {
            $data['simulate_success'] = true;
        }

        return $data;
    }

    public function render()
    {
        return view('livewire.payment-checkout', [
            'totalAmount' => $this->calculateTotalAmount(),
        ]);
    }

    protected function calculateTotalAmount(): float
    {
        if (!$this->selectedGateway) {
            return $this->booking->total_amount;
        }

        $gateway = PaymentGateway::find($this->selectedGateway);
        $gatewayFee = $gateway ? $gateway->calculateFee($this->booking->total_amount) : 0;
        $platformFee = $this->booking->total_amount * (config('payment.platform_fee_rate', 1.0) / 100);

        return $this->booking->total_amount + $gatewayFee + $platformFee;
    }
}
