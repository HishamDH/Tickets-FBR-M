<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create($service_id)
    {
        return view('booking.create', ['service_id' => $service_id]);
    }
}