<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminLoginController extends Controller
{
    /**
     * Display the admin login view.
     */
    public function create(): View
    {
        return view('auth.admin.login');
    }

    /**
     * Handle an incoming admin authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Validate the user has admin role
        $credentials = $request->validated();

        if (Auth::guard('admin')->attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::guard('admin')->user();

            // Check if user has admin role
            if ($user->role !== 'admin') {
                Auth::guard('admin')->logout();

                return back()->withErrors([
                    'email' => 'These credentials do not match our admin records.',
                ]);
            }

            $request->session()->regenerate();

            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Destroy an authenticated admin session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
}
