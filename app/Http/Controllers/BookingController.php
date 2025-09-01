<?php

namespace App\Http\Controllers;

class BookingController extends Controller
{
    public function create($service_id)
    {
        return view('booking.create', ['service_id' => $service_id]);
    }
}
