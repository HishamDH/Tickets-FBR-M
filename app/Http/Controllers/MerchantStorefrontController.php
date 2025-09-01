<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Offering;
use App\Models\User;
use Illuminate\Http\Request;

class MerchantStorefrontController extends Controller
{
    /**
     * Display merchant storefront
     */
    public function show(Request $request, $slug)
    {
        // Find merchant by business slug or ID
        $merchant = Merchant::where('business_slug', $slug)
                           ->orWhere('id', $slug)
                           ->with(['user', 'branches'])
                           ->firstOrFail();

        // Get active offerings for this merchant
        $offerings = Offering::where('user_id', $merchant->user_id)
                            ->where('status', 'active')
                            ->with(['category', 'reviews'])
                            ->withAvg('reviews', 'rating')
                            ->orderBy('created_at', 'desc')
                            ->get();

        // Track page view
        if (function_exists('set_viewed')) {
            set_viewed('merchant_storefront', $merchant->id, auth()->id());
        }

        // Determine template to use
        $template = $merchant->template ?? 'template1';
        
        return view("templates.{$template}.index", compact('merchant', 'offerings'));
    }

    /**
     * Display single offering from merchant storefront
     */
    public function showOffering(Request $request, $merchantSlug, $offeringId)
    {
        $merchant = Merchant::where('business_slug', $merchantSlug)
                           ->orWhere('id', $merchantSlug)
                           ->with(['user', 'branches'])
                           ->firstOrFail();

        $offering = Offering::where('id', $offeringId)
                           ->where('user_id', $merchant->user_id)
                           ->where('status', 'active')
                           ->with(['category', 'reviews.user'])
                           ->withAvg('reviews', 'rating')
                           ->firstOrFail();

        // Track page view
        if (function_exists('set_viewed')) {
            set_viewed('offering_detail', $offering->id, auth()->id());
        }

        $template = $merchant->template ?? 'template1';
        
        return view("templates.{$template}.offering", compact('merchant', 'offering'));
    }

    /**
     * Get merchant contact information
     */
    public function contact(Request $request, $slug)
    {
        $merchant = Merchant::where('business_slug', $slug)
                           ->orWhere('id', $slug)
                           ->with(['user', 'branches'])
                           ->firstOrFail();

        if ($request->isMethod('post')) {
            // Handle contact form submission
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'message' => 'required|string|max:1000',
            ]);

            // Create conversation or send notification
            if (class_exists('App\Models\Conversation')) {
                $conversation = \App\Models\Conversation::createConversation([
                    'type' => 'merchant_customer',
                    'customer_id' => auth()->id() ?? null,
                    'merchant_id' => $merchant->user_id,
                    'title' => 'Contact from website: ' . $request->name,
                    'initial_message' => "Name: {$request->name}\nEmail: {$request->email}\n\nMessage:\n{$request->message}",
                ]);

                return back()->with('success', 'تم إرسال رسالتك بنجاح. سيتم التواصل معك قريباً.');
            }

            // Fallback: send notification
            if (function_exists('notifcate')) {
                notifcate(
                    $merchant->user_id,
                    'رسالة جديدة من العميل',
                    "رسالة من {$request->name} ({$request->email}): {$request->message}"
                );
            }

            return back()->with('success', 'تم إرسال رسالتك بنجاح.');
        }

        $template = $merchant->template ?? 'template1';
        
        return view("templates.{$template}.contact", compact('merchant'));
    }

    /**
     * Get merchant information for API
     */
    public function apiInfo($slug)
    {
        $merchant = Merchant::where('business_slug', $slug)
                           ->orWhere('id', $slug)
                           ->with(['user', 'branches'])
                           ->firstOrFail();

        return response()->json([
            'success' => true,
            'merchant' => [
                'id' => $merchant->id,
                'business_name' => $merchant->business_name,
                'business_type' => $merchant->business_type,
                'description' => $merchant->description,
                'logo' => $merchant->logo ? asset('storage/' . $merchant->logo) : null,
                'cover_image' => $merchant->cover_image ? asset('storage/' . $merchant->cover_image) : null,
                'theme_color' => $merchant->theme_color,
                'social_links' => [
                    'facebook' => $merchant->facebook_url,
                    'instagram' => $merchant->instagram_url,
                    'whatsapp' => $merchant->whatsapp_number,
                ],
                'contact' => [
                    'phone' => $merchant->phone,
                    'email' => $merchant->email,
                    'address' => $merchant->business_address,
                ],
                'branches' => $merchant->branches->map(function ($branch) {
                    return [
                        'id' => $branch->id,
                        'name' => $branch->name,
                        'address' => $branch->address,
                        'phone' => $branch->phone,
                        'is_active' => $branch->is_active,
                    ];
                }),
            ]
        ]);
    }

    /**
     * Search merchants for public directory
     */
    public function search(Request $request)
    {
        $query = Merchant::with(['user'])
                        ->where('status', 'active')
                        ->where('is_public', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', "%{$search}%")
                  ->orWhere('business_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('business_type', $request->category);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $merchants = $query->orderBy('business_name')
                          ->paginate(12);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'merchants' => $merchants->items(),
                'pagination' => [
                    'current_page' => $merchants->currentPage(),
                    'last_page' => $merchants->lastPage(),
                    'total' => $merchants->total(),
                ]
            ]);
        }

        return view('merchants.directory', compact('merchants'));
    }
}
