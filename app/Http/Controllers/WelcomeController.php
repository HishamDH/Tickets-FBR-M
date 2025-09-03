<?php

namespace App\Http\Controllers;

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
