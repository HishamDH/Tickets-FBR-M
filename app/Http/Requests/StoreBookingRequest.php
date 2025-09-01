<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'required|date_format:H:i',
            'guest_count' => 'required|integer|min:1|max:1000',
            'duration_hours' => 'nullable|integer|min:1|max:24',
            'special_requests' => 'nullable|string|max:1000',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'number_of_tables' => 'nullable|integer|min:1|max:100',
            'notes' => 'nullable|string|max:500',

            // Payment related fields
            'payment_method' => 'nullable|string|in:card,bank_transfer,apple_pay,stc_pay,mada',
            'save_payment_method' => 'nullable|boolean',

            // Additional service specific fields
            'venue_setup' => 'nullable|string|in:theater,classroom,banquet,cocktail,u_shape,conference',
            'catering_style' => 'nullable|string|in:buffet,plated,family_style,cocktail_reception',
            'audio_visual_needs' => 'nullable|array',
            'audio_visual_needs.*' => 'string|in:microphone,projector,screen,sound_system,lighting',

            // Terms and conditions
            'accept_terms' => 'required|accepted',
            'accept_cancellation_policy' => 'required|accepted',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'booking_date.required' => 'تاريخ الحجز مطلوب',
            'booking_date.date' => 'تاريخ الحجز يجب أن يكون تاريخاً صحيحاً',
            'booking_date.after' => 'تاريخ الحجز يجب أن يكون في المستقبل',

            'booking_time.required' => 'وقت الحجز مطلوب',
            'booking_time.date_format' => 'صيغة الوقت غير صحيحة (استخدم HH:MM)',

            'guest_count.required' => 'عدد الضيوف مطلوب',
            'guest_count.integer' => 'عدد الضيوف يجب أن يكون رقماً صحيحاً',
            'guest_count.min' => 'عدد الضيوف يجب أن يكون على الأقل واحد',
            'guest_count.max' => 'عدد الضيوف لا يمكن أن يتجاوز 1000',

            'duration_hours.integer' => 'مدة الحجز يجب أن تكون رقماً صحيحاً',
            'duration_hours.min' => 'مدة الحجز يجب أن تكون ساعة واحدة على الأقل',
            'duration_hours.max' => 'مدة الحجز لا يمكن أن تتجاوز 24 ساعة',

            'special_requests.max' => 'الطلبات الخاصة لا يمكن أن تتجاوز 1000 حرف',

            'customer_name.max' => 'اسم العميل لا يمكن أن يتجاوز 255 حرف',
            'customer_phone.max' => 'رقم الهاتف لا يمكن أن يتجاوز 20 حرف',
            'customer_email.email' => 'البريد الإلكتروني يجب أن يكون صحيحاً',
            'customer_email.max' => 'البريد الإلكتروني لا يمكن أن يتجاوز 255 حرف',

            'number_of_tables.integer' => 'عدد الطاولات يجب أن يكون رقماً صحيحاً',
            'number_of_tables.min' => 'عدد الطاولات يجب أن يكون واحد على الأقل',
            'number_of_tables.max' => 'عدد الطاولات لا يمكن أن يتجاوز 100',

            'notes.max' => 'الملاحظات لا يمكن أن تتجاوز 500 حرف',

            'payment_method.in' => 'طريقة الدفع المحددة غير صحيحة',

            'venue_setup.in' => 'تخطيط القاعة المحدد غير صحيح',
            'catering_style.in' => 'نمط الضيافة المحدد غير صحيح',
            'audio_visual_needs.*.in' => 'احتياجات الصوت والصورة المحددة غير صحيحة',

            'accept_terms.required' => 'يجب الموافقة على الشروط والأحكام',
            'accept_terms.accepted' => 'يجب الموافقة على الشروط والأحكام',
            'accept_cancellation_policy.required' => 'يجب الموافقة على سياسة الإلغاء',
            'accept_cancellation_policy.accepted' => 'يجب الموافقة على سياسة الإلغاء',
        ];
    }

    /**
     * Get custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'booking_date' => 'تاريخ الحجز',
            'booking_time' => 'وقت الحجز',
            'guest_count' => 'عدد الضيوف',
            'duration_hours' => 'مدة الحجز',
            'special_requests' => 'الطلبات الخاصة',
            'customer_name' => 'اسم العميل',
            'customer_phone' => 'رقم الهاتف',
            'customer_email' => 'البريد الإلكتروني',
            'number_of_tables' => 'عدد الطاولات',
            'notes' => 'الملاحظات',
            'payment_method' => 'طريقة الدفع',
            'venue_setup' => 'تخطيط القاعة',
            'catering_style' => 'نمط الضيافة',
            'audio_visual_needs' => 'احتياجات الصوت والصورة',
            'accept_terms' => 'الموافقة على الشروط',
            'accept_cancellation_policy' => 'الموافقة على سياسة الإلغاء',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert Arabic numerals to English if needed
        if ($this->has('guest_count')) {
            $this->merge([
                'guest_count' => $this->convertArabicToEnglishNumbers($this->guest_count),
            ]);
        }

        if ($this->has('duration_hours')) {
            $this->merge([
                'duration_hours' => $this->convertArabicToEnglishNumbers($this->duration_hours),
            ]);
        }

        if ($this->has('number_of_tables')) {
            $this->merge([
                'number_of_tables' => $this->convertArabicToEnglishNumbers($this->number_of_tables),
            ]);
        }
    }

    /**
     * Convert Arabic numerals to English numerals
     */
    private function convertArabicToEnglishNumbers($value)
    {
        if (is_null($value)) {
            return $value;
        }

        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

        return str_replace($arabic, $english, $value);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom validation: Check if the selected date and time is available
            if ($this->has(['booking_date', 'booking_time'])) {
                $service = $this->route('service');

                if ($service) {
                    $bookingDateTime = $this->booking_date.' '.$this->booking_time;

                    // Check if there's a conflicting booking
                    $existingBooking = $service->bookings()
                        ->where('booking_date', $this->booking_date)
                        ->where('booking_time', $this->booking_time)
                        ->where('booking_status', '!=', 'cancelled')
                        ->exists();

                    if ($existingBooking) {
                        $validator->errors()->add('booking_time', 'هذا الوقت محجوز بالفعل، يرجى اختيار وقت آخر');
                    }

                    // Check if the service is available on the selected date
                    $dayOfWeek = date('N', strtotime($this->booking_date)); // 1 (Monday) to 7 (Sunday)
                    $availability = $service->availability()
                        ->where('day_of_week', $dayOfWeek)
                        ->where('is_available', true)
                        ->first();

                    if (! $availability) {
                        $validator->errors()->add('booking_date', 'الخدمة غير متاحة في هذا اليوم');
                    } elseif ($availability) {
                        // Check if the booking time is within service hours
                        $bookingTime = $this->booking_time;
                        if ($bookingTime < $availability->start_time || $bookingTime > $availability->end_time) {
                            $validator->errors()->add('booking_time', 'الوقت المحدد خارج ساعات العمل للخدمة');
                        }
                    }
                }
            }

            // Validate guest count against service capacity
            if ($this->has('guest_count')) {
                $service = $this->route('service');

                if ($service && $service->max_capacity && $this->guest_count > $service->max_capacity) {
                    $validator->errors()->add('guest_count', "عدد الضيوف لا يمكن أن يتجاوز {$service->max_capacity} لهذه الخدمة");
                }

                if ($service && $service->min_capacity && $this->guest_count < $service->min_capacity) {
                    $validator->errors()->add('guest_count', "عدد الضيوف يجب أن يكون {$service->min_capacity} على الأقل لهذه الخدمة");
                }
            }
        });
    }
}
