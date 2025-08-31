<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Support;

class SupportController extends Controller
{
    /**
     * Display a listing of the support tickets.
     */
    public function index()
    {
        $tickets = Support::where('user_id', auth()->id())->get();

        return response()->json($tickets);
    }

    /**
     * Store a newly created support ticket.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'open';

        $ticket = Support::create($validated);

        return response()->json(['message' => 'Support ticket created successfully', 'ticket' => $ticket]);
    }

    /**
     * Display the specified support ticket.
     */
    public function show(Support $support)
    {
        if ($support->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($support);
    }

    /**
     * Remove the specified support ticket.
     */
    public function destroy(Support $support)
    {
        if ($support->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $support->delete();

        return response()->json(['message' => 'Support ticket deleted successfully']);
    }
}
