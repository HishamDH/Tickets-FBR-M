<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display the review form for a specific service.
     */
    public function create(Service $service)
    {
        // Check if the user has booked this service before
        $hasBookedService = Booking::where('customer_id', Auth::id())
            ->where('bookable_type', Service::class)
            ->where('bookable_id', $service->id)
            ->whereIn('status', ['completed', 'paid'])
            ->exists();
            
        // Check if the user has already reviewed this service
        $hasReviewed = Review::where('user_id', Auth::id())
            ->where('service_id', $service->id)
            ->exists();
            
        if (!$hasBookedService) {
            return redirect()->route('services.show', $service)
                ->with('error', 'يمكنك فقط تقييم الخدمات التي استخدمتها سابقًا.');
        }
        
        if ($hasReviewed) {
            return redirect()->route('services.show', $service)
                ->with('error', 'لقد قمت بالفعل بتقييم هذه الخدمة.');
        }
        
        return view('reviews.create', compact('service'));
    }
    
    /**
     * Store a new review.
     */
    public function store(Request $request, Service $service)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120', // 5MB max per image
        ]);
        
        $review = new Review();
        $review->service_id = $service->id;
        $review->user_id = Auth::id();
        $review->rating = $validated['rating'];
        $review->review = $validated['review'];
        $review->is_approved = false; // Will require approval by default
        $review->save();
        
        // Handle image uploads if present
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('review_images', 'public');
                
                $review->images()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }
        }
        
        return redirect()->route('services.show', $service)
            ->with('success', 'شكرًا لك! تم إرسال تقييمك بنجاح وسيتم مراجعته قريبًا.');
    }
    
    /**
     * Display a listing of reviews for a service.
     */
    public function index(Service $service)
    {
        $reviews = $service->reviews()
            ->with('customer')
            ->where('is_approved', true)
            ->latest()
            ->paginate(10);
            
        return view('reviews.index', compact('service', 'reviews'));
    }
    
    /**
     * Display reviews by the authenticated user.
     */
    public function userReviews()
    {
        $reviews = Review::where('user_id', Auth::id())
            ->with('service')
            ->latest()
            ->paginate(10);
            
        return view('reviews.user', compact('reviews'));
    }
    
    /**
     * Show the form for editing a review.
     */
    public function edit(Review $review)
    {
        // Only allow editing of own reviews
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('reviews.user')
                ->with('error', 'لا يمكنك تعديل تقييمات المستخدمين الآخرين.');
        }
        
        // Only allow editing recent reviews (e.g. within 7 days)
        $editWindow = now()->subDays(7);
        if ($review->created_at < $editWindow) {
            return redirect()->route('reviews.user')
                ->with('error', 'لا يمكن تعديل التقييمات بعد مرور 7 أيام.');
        }
        
        $service = $review->service;
        return view('reviews.edit', compact('review', 'service'));
    }
    
    /**
     * Update the specified review.
     */
    public function update(Request $request, Review $review)
    {
        // Only allow updating own reviews
        if ($review->user_id !== Auth::id()) {
            return redirect()->route('reviews.user')
                ->with('error', 'لا يمكنك تعديل تقييمات المستخدمين الآخرين.');
        }
        
        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'review' => 'nullable|string|max:1000',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120', // 5MB max per image
        ]);
        
        $review->rating = $validated['rating'];
        $review->review = $validated['review'];
        $review->is_approved = false; // Reset approval on edit
        $review->save();
        
        // Handle new image uploads if present
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('review_images', 'public');
                
                $review->images()->create([
                    'path' => $path,
                    'type' => 'image'
                ]);
            }
        }
        
        // Handle deleted images
        if ($request->has('deleted_images')) {
            $review->images()
                ->whereIn('id', $request->deleted_images)
                ->delete();
        }
        
        return redirect()->route('reviews.user')
            ->with('success', 'تم تحديث تقييمك بنجاح وسيتم مراجعته قريبًا.');
    }
    
    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        // Only allow deleting own reviews or admin
        if ($review->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            return redirect()->route('reviews.user')
                ->with('error', 'لا يمكنك حذف تقييمات المستخدمين الآخرين.');
        }
        
        $review->delete();
        
        return back()->with('success', 'تم حذف التقييم بنجاح.');
    }
}
