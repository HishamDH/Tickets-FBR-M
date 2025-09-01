@component('mail::message')
# Order Confirmation

Hello {{ $order->billing_name }},

Thank you for your order! We're excited to confirm that your order has been successfully placed and is being processed.

## Order Details

**Order Number:** {{ $order->order_number }}  
**Order Date:** {{ $order->created_at->format('F j, Y \a\t g:i A') }}  
**Status:** {{ $order->status_label }}  
**Payment Status:** {{ $order->payment_status_label }}

## Items Ordered

@foreach($order->items as $item)
- **{{ $item->item_name }}**  
  Quantity: {{ $item->quantity }} × ${{ number_format($item->price, 2) }} = ${{ number_format($item->total, 2) }}
  @if($item->branch_info)
  
  📍 Location: {{ $item->branch_info['name'] ?? 'Branch location' }}
  @endif
  @if($item->time_slot)
  
  🕒 Scheduled: {{ $item->time_slot['date'] ?? 'Date' }} at {{ $item->time_slot['time'] ?? 'Time' }}
  @endif

@endforeach

## Order Summary

| | |
|--|--:|
| Subtotal | ${{ number_format($order->subtotal, 2) }} |
@if($order->discount > 0)
| Discount | -${{ number_format($order->discount, 2) }} |
@endif
| **Total** | **${{ number_format($order->total, 2) }} {{ $order->currency }}** |

## Billing Information

{{ $order->billing_name }}  
{{ $order->billing_details['email'] }}  
{{ $order->billing_details['phone'] }}  

{{ $order->billing_address }}

## Payment Information

**Payment Method:** {{ $order->payment_method_label }}  
@if($order->payment_status === 'completed')
**Payment Confirmed:** {{ \Carbon\Carbon::parse($order->payment_details['paid_at'])->format('F j, Y \a\t g:i A') }}
@elseif($order->payment_status === 'pending_cod')
**Payment:** Will be collected upon service delivery
@elseif($order->payment_status === 'pending')
**Payment:** Pending - please complete your payment to confirm your order
@endif

@if($order->notes)
## Order Notes

{{ $order->notes }}
@endif

## What's Next?

@if($order->payment_status === 'pending')
🔶 **Complete Payment:** Please complete your payment to confirm your order and begin processing.
@elseif($order->payment_status === 'completed')
✅ **Order Confirmed:** We've received your payment and are processing your order.
@elseif($order->payment_status === 'pending_cod')
📦 **Preparation in Progress:** We're preparing your order for delivery.
@endif

@if($order->payment_status !== 'completed')
@component('mail::button', ['url' => $orderUrl, 'color' => 'primary'])
Complete Your Order
@endcomponent
@else
@component('mail::button', ['url' => $orderUrl, 'color' => 'success'])
View Order Details
@endcomponent
@endif

## Need Help?

If you have any questions about your order, please don't hesitate to contact our support team:

📧 Email: [support@tickets-fbr.com](mailto:support@tickets-fbr.com)  
📞 Phone: +966 12 345 6789  

Our customer service team is available to assist you Monday through Sunday, 9 AM to 9 PM.

---

Thank you for choosing our services! We appreciate your business and look forward to serving you.

Best regards,  
The Tickets FBR Team

@component('mail::subcopy')
If you're having trouble clicking the button, copy and paste the URL below into your web browser:
{{ $orderUrl }}
@endcomponent
@endcomponent