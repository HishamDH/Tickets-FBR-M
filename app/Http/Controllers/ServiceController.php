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
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('description', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('location', 'LIKE', '%' . $request->search . '%');
            });
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        $services = $query->orderBy('is_featured', 'desc')
                         ->orderBy('created_at', 'desc')
                         ->paginate(12);

        $categories = Service::select('category')
                           ->distinct()
                           ->where('is_active', true)
                           ->orderBy('category')
                           ->pluck('category');

        return view('services.index', compact('services', 'categories'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $service = Service::where('is_active', true)->findOrFail($id);
        
        // Get related services (same category, excluding current service)
        $relatedServices = Service::where('category', $service->category)
                                 ->where('id', '!=', $service->id)
                                 ->where('is_active', true)
                                 ->limit(3)
                                 ->get();

        return view('services.show', compact('service', 'relatedServices'));
    }
}