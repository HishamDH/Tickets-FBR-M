<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Controller;
use App\Models\VenueLayout;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VenueLayoutController extends Controller
{
    /**
     * Show the seat designer page
     */
    public function designer(VenueLayout $venueLayout)
    {
        // Check if the venue layout belongs to the authenticated merchant
        if ($venueLayout->merchant_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue layout');
        }

        $venueLayout->load('seats');

        return view('merchant.venue-layout.designer', compact('venueLayout'));
    }

    /**
     * Update venue layout and seats
     */
    public function updateLayout(Request $request, VenueLayout $venueLayout)
    {
        // Check ownership
        if ($venueLayout->merchant_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue layout');
        }

        $request->validate([
            'layout_data' => 'required|json',
            'rows' => 'required|integer|min:1|max:50',
            'columns' => 'required|integer|min:1|max:50',
        ]);

        try {
            DB::beginTransaction();

            $layoutData = json_decode($request->layout_data, true);

            // Update venue layout
            $venueLayout->update([
                'rows' => $request->rows,
                'columns' => $request->columns,
                'layout_data' => $layoutData,
                'total_seats' => count($layoutData['seats'] ?? []),
            ]);

            // Delete existing seats
            $venueLayout->seats()->delete();

            // Create new seats
            foreach ($layoutData['seats'] ?? [] as $seatData) {
                Seat::create([
                    'venue_layout_id' => $venueLayout->id,
                    'seat_number' => $seatData['number'],
                    'row_number' => $seatData['row'],
                    'column_number' => $seatData['column'],
                    'category' => 'standard', // Default category
                    'price' => $venueLayout->offering->price ?? 0,
                    'x_position' => $seatData['column'],
                    'y_position' => $seatData['row'],
                    'is_available' => true,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Venue layout updated successfully',
                'total_seats' => count($layoutData['seats'] ?? []),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update venue layout: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get venue layout data for JavaScript
     */
    public function getLayoutData(VenueLayout $venueLayout)
    {
        // Check ownership
        if ($venueLayout->merchant_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue layout');
        }

        return response()->json([
            'success' => true,
            'data' => [
                'venue_layout' => $venueLayout,
                'seats' => $venueLayout->seats->map(function ($seat) {
                    return [
                        'id' => $seat->id,
                        'number' => $seat->seat_number,
                        'row' => $seat->row_number,
                        'column' => $seat->column_number,
                        'category' => $seat->category,
                        'price' => $seat->price,
                        'type' => 'seat',
                    ];
                }),
            ]
        ]);
    }

    /**
     * Preview venue layout
     */
    public function preview(VenueLayout $venueLayout)
    {
        // Check ownership
        if ($venueLayout->merchant_id !== Auth::id()) {
            abort(403, 'Unauthorized access to venue layout');
        }

        $venueLayout->load('seats');

        return view('merchant.venue-layout.preview', compact('venueLayout'));
    }
}
