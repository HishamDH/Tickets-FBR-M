<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    /**
     * Display the welcome page with role-based registration options
     */
    public function index()
    {
        return view('welcome');
    }
}
