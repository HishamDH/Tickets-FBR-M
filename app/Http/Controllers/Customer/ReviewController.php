<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Booking;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReviewController extends Controller
{
    /**
     * Display reviews for a service
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Review::where('customer_id', $user->id)
            ->with(['service', 'booking']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by service
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        // Search in review content
        if ($request->filled('search')) {
            $query->where('review', 'LIKE', '%' . $request->search . '%');
        }

        $reviews = $query->latest()->paginate(10);

        // Get customer's services for filter
        $customerServices = Service::whereHas('bookings', function ($q) use ($user) {
            $q->where('customer_id', $user->id);
        })->get();

        return view('customer.reviews.index', compact('reviews', 'customerServices'));
    }

    /**
     * Show form to create review for a booking
     */
    public function create(Request $request)
    {
        $bookingId = $request->booking_id;
        $user = Auth::user();

        $booking = Booking::where('id', $bookingId)
            ->where('customer_id', $user->id)
            ->with(['service', 'merchant'])
            ->firstOrFail();

        // Check if booking is completed
        if ($booking->status !== 'completed') {
            return redirect()->back()
                ->with('error', 'يمكن تقييم الخدمة فقط بعد اكتمال الحجز');
        }

        // Check if already reviewed
        $existingReview = Review::where('booking_id', $booking->id)
            ->where('customer_id', $user->id)
            ->first();

        if ($existingReview) {
            return redirect()->route('customer.reviews.show', $existingReview)
                ->with('info', 'لقد قمت بتقييم هذا الحجز مسبقاً');
        }

        return view('customer.reviews.create', compact('booking'));
    }

    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('customer_id', $user->id)
            ->with('service')
            ->firstOrFail();

        // Check if booking is completed
        if ($booking->status !== 'completed') {
            throw ValidationException::withMessages([
                'booking_id' => 'يمكن تقييم الخدمة فقط بعد اكتمال الحجز',
            ]);
        }

        // Check if already reviewed
        $existingReview = Review::where('booking_id', $booking->id)
            ->where('customer_id', $user->id)
            ->first();

        if ($existingReview) {
            throw ValidationException::withMessages([
                'booking_id' => 'لقد قمت بتقييم هذا الحجز مسبقاً',
            ]);
        }

        // Handle photo uploads
        $photos = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews', 'public');
                $photos[] = $path;
            }
        }

        // Create review
        $review = Review::create([
            'customer_id' => $user->id,
            'service_id' => $booking->service_id,
            'booking_id' => $booking->id,
            'merchant_id' => $booking->service->merchant_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'photos' => !empty($photos) ? json_encode($photos) : null,
            'is_approved' => false, // Requires admin approval
        ]);

        // Update booking with review information
        $booking->update([
            'has_review' => true,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('customer.reviews.show', $review)
            ->with('success', 'تم إرسال تقييمك بنجاح وسيتم مراجعته قبل النشر');
    }

    /**
     * Show specific review
     */
    public function show(Review $review)
    {
        $user = Auth::user();

        // Ensure user can only view their own reviews
        if ($review->customer_id !== $user->id) {
            abort(403, 'غير مصرح لك بعرض هذا التقييم');
        }

        $review->load(['service', 'booking', 'merchant']);

        return view('customer.reviews.show', compact('review'));
    }

    /**
     * Show form to edit review
     */
    public function edit(Review $review)
    {
        $user = Auth::user();

        // Ensure user can only edit their own reviews
        if ($review->customer_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم');
        }

        // Check if review can still be edited (within 7 days)
        if ($review->created_at->diffInDays(now()) > 7) {
            return redirect()->route('customer.reviews.show', $review)
                ->with('error', 'لا يمكن تعديل التقييم بعد مرور أسبوع على إنشائه');
        }

        $review->load(['service', 'booking']);

        return view('customer.reviews.edit', compact('review'));
    }

    /**
     * Update review
     */
    public function update(Request $request, Review $review)
    {
        $user = Auth::user();

        // Ensure user can only edit their own reviews
        if ($review->customer_id !== $user->id) {
            abort(403, 'غير مصرح لك بتعديل هذا التقييم');
        }

        // Check if review can still be edited
        if ($review->created_at->diffInDays(now()) > 7) {
            return redirect()->route('customer.reviews.show', $review)
                ->with('error', 'لا يمكن تعديل التقييم بعد مرور أسبوع على إنشائه');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'required|string|min:10|max:1000',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'remove_photos.*' => 'nullable|string', // URLs of photos to remove
        ]);

        // Handle existing photos
        $existingPhotos = $review->photos ? json_decode($review->photos, true) : [];
        
        // Remove selected photos
        if ($request->filled('remove_photos')) {
            foreach ($request->remove_photos as $photoToRemove) {
                $existingPhotos = array_filter($existingPhotos, function ($photo) use ($photoToRemove) {
                    return $photo !== $photoToRemove;
                });
                
                // Delete file from storage
                if (file_exists(storage_path('app/public/' . $photoToRemove))) {
                    unlink(storage_path('app/public/' . $photoToRemove));
                }
            }
        }

        // Add new photos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('reviews', 'public');
                $existingPhotos[] = $path;
            }
        }

        // Update review
        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'photos' => !empty($existingPhotos) ? json_encode(array_values($existingPhotos)) : null,
            'is_approved' => false, // Requires re-approval after edit
            'updated_at' => now(),
        ]);

        return redirect()->route('customer.reviews.show', $review)
            ->with('success', 'تم تحديث تقييمك بنجاح وسيتم مراجعته مرة أخرى');
    }

    /**
     * Delete review
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();

        // Ensure user can only delete their own reviews
        if ($review->customer_id !== $user->id) {
            abort(403, 'غير مصرح لك بحذف هذا التقييم');
        }

        // Delete photos from storage
        if ($review->photos) {
            $photos = json_decode($review->photos, true);
            foreach ($photos as $photo) {
                if (file_exists(storage_path('app/public/' . $photo))) {
                    unlink(storage_path('app/public/' . $photo));
                }
            }
        }

        // Update booking
        if ($review->booking) {
            $review->booking->update([
                'has_review' => false,
                'reviewed_at' => null,
            ]);
        }

        $review->delete();

        return redirect()->route('customer.reviews.index')
            ->with('success', 'تم حذف التقييم بنجاح');
    }

    /**
     * Display public reviews for a service
     */
    public function publicReviews(Service $service)
    {
        $reviews = Review::where('service_id', $service->id)
            ->where('is_approved', true)
            ->with(['customer'])
            ->latest()
            ->paginate(10);

        $averageRating = $reviews->avg('rating');
        $ratingDistribution = Review::where('service_id', $service->id)
            ->where('is_approved', true)
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating');

        return view('public.reviews', compact('service', 'reviews', 'averageRating', 'ratingDistribution'));
    }

    /**
     * Get reviews summary for AJAX requests
     */
    public function getReviewsSummary(Service $service)
    {
        $totalReviews = Review::where('service_id', $service->id)
            ->where('is_approved', true)
            ->count();

        $averageRating = Review::where('service_id', $service->id)
            ->where('is_approved', true)
            ->avg('rating');

        $ratingDistribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $count = Review::where('service_id', $service->id)
                ->where('is_approved', true)
                ->where('rating', $i)
                ->count();
            
            $ratingDistribution[$i] = [
                'count' => $count,
                'percentage' => $totalReviews > 0 ? round(($count / $totalReviews) * 100, 1) : 0,
            ];
        }

        return response()->json([
            'total_reviews' => $totalReviews,
            'average_rating' => round($averageRating, 1),
            'rating_distribution' => $ratingDistribution,
        ]);
    }
}
