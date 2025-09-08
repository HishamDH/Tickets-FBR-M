<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    /**
     * Display available services
     */
    public function index(Request $request)
    {
        $query = Service::with(['merchant'])
            ->where('is_active', true)
            ->whereHas('merchant', function ($q) {
                $q->where('status', 'active');
            });

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->whereHas('merchant', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search by name or description
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhereHas('merchant', function ($mq) use ($search) {
                        $mq->where('business_name', 'like', "%{$search}%");
                    });
            });
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'popular':
                $query->withCount('bookings')->orderBy('bookings_count', 'desc');
                break;
            default:
                $query->latest();
        }

        $services = $query->paginate(12);

        // Get filter options
        $categories = Service::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->map(function ($category) {
                return [
                    'value' => $category,
                    'label' => $this->getCategoryLabel($category),
                ];
            });

        $cities = Merchant::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->whereHas('user', function ($q) {
                $q->where('status', 'active');
            })
            ->pluck('city');

        return view('customer.services.index', compact(
            'services',
            'categories',
            'cities'
        ));
    }

    /**
     * Show service details
     */
    public function show(Service $service)
    {
        if (! $service->is_active || $service->merchant->status !== 'active') {
            abort(404, 'الخدمة غير متاحة');
        }

        $service->load([
            'merchant',
            'availability',
            'reviews' => function ($query) {
                $query->with(['customer' => function ($q) {
                    $q->select('id', 'name');
                }])
                ->latest()
                ->limit(5);
            },
        ]);

        // Get related services from same merchant
        $relatedServices = Service::where('merchant_id', $service->merchant_id)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        // Get similar services from other merchants
        $similarServices = Service::where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->whereHas('merchant', function ($q) {
                $q->where('status', 'active');
            })
            ->limit(4)
            ->get();

        // Check if user has booked this service before
        $hasBooked = false;
        if (Auth::guard('customer')->check()) {
            $hasBooked = Auth::guard('customer')->user()->bookings()
                ->where('service_id', $service->id)
                ->exists();
        }

        return view('customer.services.show', compact(
            'service',
            'relatedServices',
            'similarServices',
            'hasBooked'
        ));
    }

    /**
     * Add service to favorites
     */
    public function addToFavorites(Service $service)
    {
        $user = Auth::guard('customer')->user();

        if (! $user->favoriteServices()->where('service_id', $service->id)->exists()) {
            $user->favoriteServices()->attach($service->id);

            return response()->json([
                'success' => true,
                'message' => 'تمت إضافة الخدمة إلى المفضلة',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'الخدمة موجودة بالفعل في المفضلة',
        ]);
    }

    /**
     * Remove service from favorites
     */
    public function removeFromFavorites(Service $service)
    {
        $user = Auth::guard('customer')->user();

        $user->favoriteServices()->detach($service->id);

        return response()->json([
            'success' => true,
            'message' => 'تم حذف الخدمة من المفضلة',
        ]);
    }

    /**
     * Book a service
     */
    public function book(Request $request, Service $service)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required',
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = Auth::guard('customer')->user();
        
        // Calculate total amount
        $totalAmount = $service->price * $request->quantity;
        
        // Create booking
        $booking = $user->bookings()->create([
            'service_id' => $service->id,
            'merchant_id' => $service->merchant_id,
            'quantity' => $request->quantity,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'total_amount' => $totalAmount,
            'notes' => $request->notes,
            'status' => 'pending'
        ]);

        return redirect()->route('customer.bookings.show', $booking->id)
            ->with('success', 'تم إرسال طلب الحجز بنجاح! سيتم التواصل معك قريباً.');
    }

    /**
     * Toggle service favorite status
     */
    public function toggleFavorite(Service $service)
    {
        $user = Auth::guard('customer')->user();

        if ($user->favoriteServices()->where('service_id', $service->id)->exists()) {
            $user->favoriteServices()->detach($service->id);
            $message = 'تم حذف الخدمة من المفضلة';
            $action = 'removed';
        } else {
            $user->favoriteServices()->attach($service->id);
            $message = 'تمت إضافة الخدمة إلى المفضلة';
            $action = 'added';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'action' => $action
        ]);
    }

    /**
     * Get category label in Arabic
     */
    private function getCategoryLabel(string $category): string
    {
        $labels = [
            'venues' => 'قاعات وأماكن',
            'catering' => 'تموين وضيافة',
            'photography' => 'تصوير',
            'entertainment' => 'ترفيه',
            'planning' => 'تنظيم فعاليات',
            'decoration' => 'تزيين',
            'transportation' => 'نقل ومواصلات',
            'audio_visual' => 'صوتيات ومرئيات',
            'security' => 'أمن وحراسة',
            'cleaning' => 'تنظيف',
            'flowers' => 'ورود وأزهار',
            'gifts' => 'هدايا وتذكارات',
        ];

        return $labels[$category] ?? $category;
    }
}
