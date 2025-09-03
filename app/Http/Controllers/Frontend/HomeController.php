<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Merchant;
use App\Models\Offering;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredMerchants = Merchant::with('user')
            ->where('verification_status', 'approved')
            ->take(8)
            ->get();

        $categories = Category::take(6)->get();

        $featuredOfferings = Offering::with(['user'])
            ->where('status', 'active')
            ->take(6)
            ->get();

        return view('frontend.home', compact('featuredMerchants', 'categories', 'featuredOfferings'));
    }

    public function merchants(Request $request)
    {
        $merchants = Merchant::with(['user'])
            ->where('verification_status', 'approved')
            ->when($request->get('search'), function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('business_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->get('category'), function ($query, $category) {
                $query->whereHas('offerings', function ($q) use ($category) {
                    $q->where('category', $category);
                });
            })
            ->withCount('offerings')
            ->paginate(12);

        return view('frontend.merchants', compact('merchants'));
    }

    public function merchantShow($id)
    {
        $merchant = Merchant::with(['user'])->findOrFail($id);

        // Get offerings for this merchant
        $offerings = Offering::where('user_id', $merchant->user_id)
            ->where('status', 'active')
            ->get();

        $merchant->offerings = $offerings;

        return view('frontend.merchant-show', compact('merchant'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $category = $request->get('category');

        $offerings = Offering::with(['user'])
            ->where('status', 'active')
            ->when($query, function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->when($category, function ($q) use ($category) {
                $q->where('category', $category);
            })
            ->paginate(12);

        $categories = Category::all();

        return view('frontend.search', compact('offerings', 'categories', 'query', 'category'));
    }
}
