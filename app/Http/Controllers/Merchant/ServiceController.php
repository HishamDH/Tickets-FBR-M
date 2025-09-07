<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display merchant's services
     */
    public function merchantIndex(Request $request)
    {
        $user = Auth::user();
        $merchantId = $user->id;

        $query = Service::where('merchant_id', $merchantId);

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%');
            });
        }

        $services = $query->withCount(['bookings', 'activeBookings'])
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get categories for filter
        $categories = Service::where('merchant_id', $merchantId)
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        // Calculate summary statistics
        $stats = $this->getServiceStats($merchantId);

        return view('merchant.services.index', compact('services', 'categories', 'stats'));
    }

    /**
     * Show the form for creating a new service
     */
    public function create()
    {
        $categories = $this->getServiceCategories();
        
        return view('merchant.services.create', compact('categories'));
    }

    /**
     * Store a newly created service
     */
    public function store(Request $request)
    {
        \Log::info('Service creation attempt', $request->all());
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $serviceData = $request->all();
        $serviceData['merchant_id'] = $user->id;
        $serviceData['is_active'] = $request->has('is_active');
        $serviceData['is_available'] = $request->has('is_available');
        $serviceData['is_featured'] = $request->has('is_featured');
        
        // Convert features to array if provided
        if ($request->filled('features')) {
            $features = array_map('trim', explode(',', $request->features));
            $serviceData['features'] = array_filter($features);
        }

        // Handle main image upload
        if ($request->hasFile('image')) {
            $serviceData['image'] = $request->file('image')->store('services', 'public');
        }

        // Set default values
        $serviceData['currency'] = 'SAR';
        $serviceData['price_type'] = 'fixed';
        $serviceData['pricing_model'] = 'fixed';
        $serviceData['service_type'] = 'service';
        $serviceData['status'] = 'active';
        $serviceData['online_booking_enabled'] = true;

        $service = Service::create($serviceData);

        return redirect()->route('merchant.services.show', $service)
                        ->with('success', 'تم إنشاء الخدمة بنجاح');
    }

    /**
     * Display the specified service
     */
    public function show($id)
    {
        $user = Auth::user();
        $service = Service::where('merchant_id', $user->id)->findOrFail($id);

        // Get recent bookings for this service
        $recentBookings = Booking::where('service_id', $service->id)
            ->with('customer')
            ->latest()
            ->limit(10)
            ->get();

        // Get booking statistics
        $bookingStats = [
            'total' => $service->bookings()->count(),
            'completed' => $service->bookings()->where('status', 'completed')->count(),
            'pending' => $service->bookings()->where('status', 'pending')->count(),
            'cancelled' => $service->bookings()->where('status', 'cancelled')->count(),
            'revenue' => $service->bookings()->where('payment_status', 'paid')->sum('total_amount'),
        ];

        return view('merchant.services.show', compact('service', 'recentBookings', 'bookingStats'));
    }

    /**
     * Show the form for editing the specified service
     */
    public function edit($id)
    {
        $user = Auth::user();
        $service = Service::where('merchant_id', $user->id)->findOrFail($id);
        $categories = $this->getServiceCategories();

        return view('merchant.services.edit', compact('service', 'categories'));
    }

    /**
     * Update the specified service
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $service = Service::where('merchant_id', $user->id)->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'location' => 'nullable|string|max:255',
            'capacity' => 'nullable|integer|min:1',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'features' => 'nullable|string',
            'is_active' => 'boolean',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $serviceData = $request->all();
        $serviceData['is_active'] = $request->has('is_active');
        $serviceData['is_available'] = $request->has('is_available');
        $serviceData['is_featured'] = $request->has('is_featured');
        
        // Convert features to array if provided
        if ($request->filled('features')) {
            $features = array_map('trim', explode(',', $request->features));
            $serviceData['features'] = array_filter($features);
        }

        // Handle main image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            $serviceData['image'] = $request->file('image')->store('services', 'public');
        }


        $service->update($serviceData);

        return redirect()->route('merchant.services.show', $service)
                        ->with('success', 'تم تحديث الخدمة بنجاح');
    }

    /**
     * Remove the specified service
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $service = Service::where('merchant_id', $user->id)->findOrFail($id);

        // Check if service has active bookings
        $activeBookings = $service->bookings()
            ->whereIn('status', ['pending', 'confirmed'])
            ->count();

        if ($activeBookings > 0) {
            return redirect()->back()
                           ->with('error', 'لا يمكن حذف الخدمة لوجود حجوزات نشطة عليها');
        }

        // Delete images
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }

        if ($service->gallery) {
            $gallery = json_decode($service->gallery, true);
            foreach ($gallery as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $service->delete();

        return redirect()->route('merchant.services.index')
                        ->with('success', 'تم حذف الخدمة بنجاح');
    }

    /**
     * Toggle service status
     */
    public function toggleStatus($id)
    {
        $user = Auth::user();
        $service = Service::where('merchant_id', $user->id)->findOrFail($id);

        $service->update([
            'is_active' => !$service->is_active
        ]);

        $status = $service->is_active ? 'تم تفعيل' : 'تم إلغاء تفعيل';
        
        return redirect()->back()
                        ->with('success', $status . ' الخدمة بنجاح');
    }

    /**
     * Clone service
     */
    public function clone($id)
    {
        $user = Auth::user();
        $service = Service::where('merchant_id', $user->id)->findOrFail($id);

        $newService = $service->replicate();
        $newService->name = $service->name . ' (نسخة)';
        $newService->is_featured = false;
        $newService->save();

        return redirect()->route('merchant.services.edit', $newService)
                        ->with('success', 'تم نسخ الخدمة بنجاح');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'selected_services' => 'required|array',
            'selected_services.*' => 'exists:services,id',
        ]);

        $user = Auth::user();
        $services = Service::where('merchant_id', $user->id)
                          ->whereIn('id', $request->selected_services);

        switch ($request->action) {
            case 'activate':
                $services->update(['is_active' => true]);
                $message = 'تم تفعيل الخدمات المحددة';
                break;

            case 'deactivate':
                $services->update(['is_active' => false]);
                $message = 'تم إلغاء تفعيل الخدمات المحددة';
                break;

            case 'delete':
                // Check for active bookings
                $servicesWithBookings = $services->whereHas('bookings', function ($q) {
                    $q->whereIn('status', ['pending', 'confirmed']);
                })->count();

                if ($servicesWithBookings > 0) {
                    return redirect()->back()
                                   ->with('error', 'لا يمكن حذف بعض الخدمات لوجود حجوزات نشطة عليها');
                }

                $services->delete();
                $message = 'تم حذف الخدمات المحددة';
                break;
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get service statistics
     */
    private function getServiceStats($merchantId)
    {
        $totalServices = Service::where('merchant_id', $merchantId)->count();
        $activeServices = Service::where('merchant_id', $merchantId)->where('is_active', true)->count();
        $featuredServices = Service::where('merchant_id', $merchantId)->where('is_featured', true)->count();
        
        $totalBookings = Booking::whereHas('service', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        })->count();

        $totalRevenue = Booking::whereHas('service', function ($q) use ($merchantId) {
            $q->where('merchant_id', $merchantId);
        })->where('payment_status', 'paid')->sum('total_amount');

        return [
            'total_services' => $totalServices,
            'active_services' => $activeServices,
            'inactive_services' => $totalServices - $activeServices,
            'featured_services' => $featuredServices,
            'total_bookings' => $totalBookings,
            'total_revenue' => $totalRevenue,
            'average_rating' => 0, // TODO: Calculate when rating system is implemented
        ];
    }

    /**
     * Get available service categories
     */
    private function getServiceCategories()
    {
        return [
            'رياضة وصحة' => 'رياضة وصحة',
            'تعليم وتدريب' => 'تعليم وتدريب',
            'طعام ومشروبات' => 'طعام ومشروبات',
            'نقل ومواصلات' => 'نقل ومواصلات',
            'صحة وجمال' => 'صحة وجمال',
            'ترفيه وفعاليات' => 'ترفيه وفعاليات',
            'خدمات منزلية' => 'خدمات منزلية',
            'استشارات مهنية' => 'استشارات مهنية',
            'تكنولوجيا' => 'تكنولوجيا',
            'أخرى' => 'أخرى',
        ];
    }
}
