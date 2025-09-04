<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Merchant;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class SubdomainStorefrontController extends Controller
{
    protected $merchant;
    
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->merchant = $request->attributes->get('merchant');
            
            if (!$this->merchant) {
                abort(404, 'Merchant not found');
            }
            
            return $next($request);
        });
    }
    
    /**
     * Show merchant storefront homepage
     */
    public function index()
    {
        $featuredServices = $this->merchant->services()
            ->where('is_featured', true)
            ->where('is_active', true)
            ->limit(6)
            ->get();
            
        $allServices = $this->merchant->services()
            ->where('is_active', true)
            ->limit(12)
            ->get();
        
        return view('subdomain.index', compact('featuredServices', 'allServices'));
    }
    
    /**
     * Show all services
     */
    public function services(Request $request)
    {
        $query = $this->merchant->services()->where('is_active', true);
        
        // Filter by category
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        // Search by name/description
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sort = $request->get('sort', 'name');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }
        
        $services = $query->paginate(12);
        
        $categories = $this->merchant->services()
            ->where('is_active', true)
            ->select('category')
            ->distinct()
            ->pluck('category');
        
        return view('subdomain.services', compact('services', 'categories'));
    }
    
    /**
     * Show individual service
     */
    public function service(Service $service)
    {
        // Ensure service belongs to this merchant
        if ($service->merchant_id !== $this->merchant->user_id) {
            abort(404);
        }
        
        $relatedServices = $this->merchant->services()
            ->where('id', '!=', $service->id)
            ->where('category', $service->category)
            ->where('is_active', true)
            ->limit(4)
            ->get();
            
        return view('subdomain.service', compact('service', 'relatedServices'));
    }
    
    /**
     * Show booking form
     */
    public function bookingForm(Service $service)
    {
        // Ensure service belongs to this merchant
        if ($service->merchant_id !== $this->merchant->user_id) {
            abort(404);
        }
        
        if (!$service->isBookable()) {
            return redirect()->back()->with('error', 'This service is not available for booking.');
        }
        
        return view('subdomain.booking-form', compact('service'));
    }
    
    /**
     * Process booking
     */
    public function processBooking(Request $request, Service $service)
    {
        // Ensure service belongs to this merchant
        if ($service->merchant_id !== $this->merchant->user_id) {
            abort(404);
        }
        
        $validator = Validator::make($request->all(), [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'booking_date' => 'required|date|after:today',
            'booking_time' => 'nullable|string',
            'guest_count' => 'nullable|integer|min:1',
            'special_requests' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Calculate pricing
        $basePrice = $service->price;
        $guestCount = $request->guest_count ?? 1;
        
        if ($service->pricing_model === 'per_person') {
            $totalAmount = $basePrice * $guestCount;
        } else {
            $totalAmount = $basePrice;
        }
        
        // Calculate commission
        $commissionRate = $this->merchant->commission_rate ?? 10;
        $commissionAmount = ($totalAmount * $commissionRate) / 100;
        
        // Create booking
        $booking = Booking::create([
            'service_id' => $service->id,
            'merchant_id' => $this->merchant->user_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'guest_count' => $guestCount,
            'total_amount' => $totalAmount,
            'commission_amount' => $commissionAmount,
            'commission_rate' => $commissionRate,
            'payment_status' => 'pending',
            'status' => 'pending',
            'booking_source' => 'subdomain',
            'special_requests' => $request->special_requests,
        ]);
        
        return redirect()->route('subdomain.booking.confirmation', $booking);
    }
    
    /**
     * Show booking confirmation
     */
    public function bookingConfirmation(Booking $booking)
    {
        // Ensure booking belongs to this merchant
        if ($booking->merchant_id !== $this->merchant->user_id) {
            abort(404);
        }
        
        return view('subdomain.booking-confirmation', compact('booking'));
    }
    
    /**
     * Show about page
     */
    public function about()
    {
        return view('subdomain.about');
    }
    
    /**
     * Show contact page
     */
    public function contact()
    {
        return view('subdomain.contact');
    }
    
    /**
     * Submit contact form
     */
    public function submitContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        // Here you would typically send an email or save to database
        // For now, just return success message
        
        return redirect()->back()->with('success', 'Thank you for your message. We will get back to you soon.');
    }
    
    /**
     * Show gallery
     */
    public function gallery()
    {
        $services = $this->merchant->services()
            ->where('is_active', true)
            ->whereNotNull('images')
            ->get();
            
        return view('subdomain.gallery', compact('services'));
    }
    
    /**
     * Show reviews
     */
    public function reviews()
    {
        // This would be implemented when review system is complete
        return view('subdomain.reviews');
    }
    
    /**
     * API: Get merchant info
     */
    public function apiInfo()
    {
        return response()->json([
            'merchant' => [
                'id' => $this->merchant->id,
                'business_name' => $this->merchant->business_name,
                'business_type' => $this->merchant->business_type,
                'city' => $this->merchant->city,
                'subdomain' => $this->merchant->subdomain,
                'logo_url' => $this->merchant->logo_url,
                'primary_color' => $this->merchant->primary_color,
                'secondary_color' => $this->merchant->secondary_color,
                'accent_color' => $this->merchant->accent_color,
            ]
        ]);
    }
    
    /**
     * API: Get services
     */
    public function apiServices()
    {
        $services = $this->merchant->services()
            ->where('is_active', true)
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'price' => $service->price,
                    'price_formatted' => $service->price_formatted,
                    'category' => $service->category,
                    'service_type' => $service->service_type,
                    'is_featured' => $service->is_featured,
                    'images' => $service->images,
                ];
            });
            
        return response()->json(['services' => $services]);
    }
    
    /**
     * API: Get service availability
     */
    public function apiAvailability(Service $service, Request $request)
    {
        // Ensure service belongs to this merchant
        if ($service->merchant_id !== $this->merchant->user_id) {
            abort(404);
        }
        
        $date = $request->get('date', now()->format('Y-m-d'));
        
        // Get existing bookings for the date
        $existingBookings = Booking::where('service_id', $service->id)
            ->whereDate('booking_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('booking_time')
            ->toArray();
        
        // Generate available time slots (simplified)
        $availableSlots = [];
        for ($hour = 9; $hour <= 21; $hour++) {
            $time = sprintf('%02d:00', $hour);
            if (!in_array($time, $existingBookings)) {
                $availableSlots[] = $time;
            }
        }
        
        return response()->json([
            'date' => $date,
            'available_slots' => $availableSlots,
            'existing_bookings' => count($existingBookings),
        ]);
    }
}