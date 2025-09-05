<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * Display a listing of services
     */
    public function index(Request $request): JsonResponse
    {
        $request->validate([
            'category' => 'sometimes|string',
            'service_type' => 'sometimes|string',
            'location' => 'sometimes|string',
            'min_price' => 'sometimes|numeric|min:0',
            'max_price' => 'sometimes|numeric|min:0',
            'merchant_id' => 'sometimes|exists:users,id',
            'featured' => 'sometimes|boolean',
            'available' => 'sometimes|boolean',
            'capacity' => 'sometimes|integer|min:1',
            'search' => 'sometimes|string|max:255',
            'sort_by' => ['sometimes', Rule::in(['name', 'price', 'created_at', 'updated_at', 'category'])],
            'sort_direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        $query = Service::query()
            ->with(['merchant', 'bookings', 'availability']);

        // Only show active services for non-authenticated users or customers
        if (!$request->user() || $request->user()->isCustomer()) {
            $query->where('is_active', true)
                  ->where('is_available', true);
        }

        // Apply filters
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('service_type')) {
            $query->where('service_type', $request->service_type);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        if ($request->filled('min_price')) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('price', '>=', $request->min_price)
                  ->orWhere('base_price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->where(function (Builder $q) use ($request) {
                $q->where('price', '<=', $request->max_price)
                  ->orWhere('base_price', '<=', $request->max_price);
            });
        }

        if ($request->filled('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        if ($request->filled('featured')) {
            $query->where('is_featured', $request->boolean('featured'));
        }

        if ($request->filled('available')) {
            $query->where('is_available', $request->boolean('available'));
        }

        if ($request->filled('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%')
                  ->orWhere('location', 'like', '%' . $searchTerm . '%')
                  ->orWhereHas('merchant', function (Builder $merchantQuery) use ($searchTerm) {
                      $merchantQuery->where('name', 'like', '%' . $searchTerm . '%')
                                   ->orWhere('business_name', 'like', '%' . $searchTerm . '%');
                  });
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginate results
        $perPage = $request->get('per_page', 15);
        $services = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services->items()),
            'meta' => [
                'current_page' => $services->currentPage(),
                'last_page' => $services->lastPage(),
                'per_page' => $services->perPage(),
                'total' => $services->total(),
                'from' => $services->firstItem(),
                'to' => $services->lastItem(),
            ],
            'links' => [
                'first' => $services->url(1),
                'last' => $services->url($services->lastPage()),
                'prev' => $services->previousPageUrl(),
                'next' => $services->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified service
     */
    public function show(Request $request, Service $service): JsonResponse
    {
        // Load relationships
        $service->load(['merchant', 'bookings', 'availability']);

        // Check if service is accessible
        if (!$this->canAccessService($request, $service)) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found or not accessible.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ServiceResource($service),
        ]);
    }

    /**
     * Store a newly created service (for merchants)
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', Service::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
            'category' => 'required|string',
            'service_type' => 'required|string',
            'pricing_model' => 'required|in:fixed,per_person,per_hour,per_table,package',
            'capacity' => 'sometimes|integer|min:1',
            'duration_hours' => 'sometimes|integer|min:1',
            'features' => 'sometimes|array',
            'images' => 'sometimes|array',
            'images.*' => 'string',
            'is_featured' => 'sometimes|boolean',
            'online_booking_enabled' => 'sometimes|boolean',
        ]);

        $validated['merchant_id'] = $request->user()->id;
        $validated['status'] = 'active';
        $validated['is_active'] = true;
        $validated['is_available'] = true;

        $service = Service::create($validated);
        $service->load(['merchant', 'availability']);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully.',
            'data' => new ServiceResource($service),
        ], 201);
    }

    /**
     * Update the specified service (for merchants)
     */
    public function update(Request $request, Service $service): JsonResponse
    {
        $this->authorize('update', $service);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'location' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric|min:0',
            'currency' => 'sometimes|string|max:3',
            'category' => 'sometimes|string',
            'service_type' => 'sometimes|string',
            'pricing_model' => 'sometimes|in:fixed,per_person,per_hour,per_table,package',
            'capacity' => 'sometimes|integer|min:1',
            'duration_hours' => 'sometimes|integer|min:1',
            'features' => 'sometimes|array',
            'images' => 'sometimes|array',
            'images.*' => 'string',
            'is_featured' => 'sometimes|boolean',
            'is_active' => 'sometimes|boolean',
            'is_available' => 'sometimes|boolean',
            'online_booking_enabled' => 'sometimes|boolean',
        ]);

        $service->update($validated);
        $service->load(['merchant', 'availability']);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully.',
            'data' => new ServiceResource($service),
        ]);
    }

    /**
     * Remove the specified service (for merchants)
     */
    public function destroy(Request $request, Service $service): JsonResponse
    {
        $this->authorize('delete', $service);

        // Check if service has active bookings
        if ($service->bookings()->whereIn('status', ['pending', 'confirmed'])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete service with active bookings.'
            ], 422);
        }

        $service->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully.',
        ]);
    }

    /**
     * Get service categories
     */
    public function categories(Request $request): JsonResponse
    {
        $categories = [
            'venues' => [
                'name' => 'Venues & Locations',
                'name_arabic' => 'قاعات ومواقع',
                'icon' => '🏛️',
            ],
            'catering' => [
                'name' => 'Catering & Hospitality',
                'name_arabic' => 'تموين وضيافة',
                'icon' => '🍽️',
            ],
            'photography' => [
                'name' => 'Photography',
                'name_arabic' => 'تصوير',
                'icon' => '📸',
            ],
            'beauty' => [
                'name' => 'Beauty Services',
                'name_arabic' => 'تجميل',
                'icon' => '💄',
            ],
            'entertainment' => [
                'name' => 'Entertainment',
                'name_arabic' => 'ترفيه',
                'icon' => '🎭',
            ],
            'transportation' => [
                'name' => 'Transportation',
                'name_arabic' => 'نقل ومواصلات',
                'icon' => '🚗',
            ],
            'security' => [
                'name' => 'Security Services',
                'name_arabic' => 'أمن وحراسة',
                'icon' => '🛡️',
            ],
            'flowers_invitations' => [
                'name' => 'Flowers & Invitations',
                'name_arabic' => 'ورود ودعوات',
                'icon' => '🌹',
            ],
        ];

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Get featured services
     */
    public function featured(Request $request): JsonResponse
    {
        $request->validate([
            'limit' => 'sometimes|integer|min:1|max:20',
            'category' => 'sometimes|string',
        ]);

        $query = Service::query()
            ->with(['merchant'])
            ->where('is_featured', true)
            ->where('is_active', true)
            ->where('is_available', true);

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $limit = $request->get('limit', 10);
        $services = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services),
        ]);
    }

    /**
     * Search services
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2|max:255',
            'category' => 'sometimes|string',
            'location' => 'sometimes|string',
            'limit' => 'sometimes|integer|min:1|max:50',
        ]);

        $query = Service::query()
            ->with(['merchant'])
            ->where('is_active', true)
            ->where('is_available', true);

        $searchTerm = $request->q;
        $query->where(function (Builder $q) use ($searchTerm) {
            $q->where('name', 'like', '%' . $searchTerm . '%')
              ->orWhere('description', 'like', '%' . $searchTerm . '%')
              ->orWhere('location', 'like', '%' . $searchTerm . '%')
              ->orWhereHas('merchant', function (Builder $merchantQuery) use ($searchTerm) {
                  $merchantQuery->where('name', 'like', '%' . $searchTerm . '%')
                               ->orWhere('business_name', 'like', '%' . $searchTerm . '%');
              });
        });

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $limit = $request->get('limit', 20);
        $services = $query->limit($limit)->get();

        return response()->json([
            'success' => true,
            'data' => ServiceResource::collection($services),
            'meta' => [
                'query' => $searchTerm,
                'total_results' => $services->count(),
                'applied_filters' => array_filter([
                    'category' => $request->category,
                    'location' => $request->location,
                ]),
            ],
        ]);
    }

    /**
     * Check if user can access the service
     */
    private function canAccessService(Request $request, Service $service): bool
    {
        $user = $request->user();

        // Admins can access any service
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Merchants can access their own services (even inactive ones)
        if ($user && $user->isMerchant() && $service->merchant_id === $user->id) {
            return true;
        }

        // Other users can only access active and available services
        return $service->is_active && $service->is_available;
    }
}