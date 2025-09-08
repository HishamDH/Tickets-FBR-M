<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerLoginController extends Controller
{
    /**
     * Display the customer login view.
     */
    public function create(): View
    {
        return view('auth.customer.login');
    }

    /**
     * Handle an incoming customer authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate the user has customer role
        $credentials = $request->validated();

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('customer')->user();

            // Check if user has customer role
            if ($user->role !== 'customer') {
                Auth::guard('customer')->logout();

                return back()->withErrors([
                    'email' => 'These credentials do not match our customer records.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('/customer/services');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated customer session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/customer/login');
    }
}
