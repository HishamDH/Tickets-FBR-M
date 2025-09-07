<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Service::query()->where('is_active', true);

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('description', 'LIKE', '%'.$request->search.'%')
                    ->orWhere('location', 'LIKE', '%'.$request->search.'%');
            });
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }
        
        // Rating filter - filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->where('is_approved', true)
                  ->groupBy('service_id')
                  ->havingRaw('AVG(rating) >= ?', [$request->min_rating]);
            });
        }
        
        // Sort by highest rated
        if ($request->sort === 'highest_rated') {
            $query->withAvg('reviews', 'rating')
                ->orderByDesc('reviews_avg_rating');
        }
        // Sort by most reviewed
        elseif ($request->sort === 'most_reviewed') {
            $query->withCount(['reviews' => function ($q) {
                $q->where('is_approved', true);
            }])
            ->orderByDesc('reviews_count');
        }
        // Sort by price (low to high)
        elseif ($request->sort === 'price_low') {
            $query->orderBy('price', 'asc');
        }
        // Sort by price (high to low)
        elseif ($request->sort === 'price_high') {
            $query->orderBy('price', 'desc');
        }
        // Default sort
        else {
            $query->orderBy('is_featured', 'desc')
                  ->orderBy('created_at', 'desc');
        }

        $services = $query->paginate(12);

        $categories = Service::select('category')
            ->distinct()
            ->where('is_active', true)
            ->orderBy('category')
            ->pluck('category');

        // Return JSON for API requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $services->items(),
                'meta' => [
                    'current_page' => $services->currentPage(),
                    'last_page' => $services->lastPage(),
                    'per_page' => $services->perPage(),
                    'total' => $services->total(),
                ],
                'categories' => $categories
            ]);
        }

        return view('services.index', compact('services', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $id)
    {
        $service = Service::where('is_active', true)->findOrFail($id);

        // Get related services (same category, excluding current service)
        $relatedServices = Service::where('category', $service->category)
            ->where('id', '!=', $service->id)
            ->where('is_active', true)
            ->limit(3)
            ->get();

        // Get reviews data
        $reviews = $service->reviews()->where('is_approved', true)->get();
        $totalReviews = $reviews->count();
        $avgRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;
        
        // Calculate rating distribution
        $ratingDistribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        foreach ($reviews as $review) {
            $ratingDistribution[$review->rating]++;
        }

        // Return JSON for API requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $service,
                'related_services' => $relatedServices,
                'avg_rating' => $avgRating,
                'total_reviews' => $totalReviews
            ]);
        }

        return view('services.show', compact('service', 'relatedServices', 'avgRating', 'totalReviews', 'ratingDistribution'));
    }
}
