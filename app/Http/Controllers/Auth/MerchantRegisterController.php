<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class MerchantRegisterController extends Controller
{
    /**
     * Display the merchant registration view.
     */
    public function create(): View
    {
        return view('auth.merchant.register');
    }

    /**
     * Handle an incoming merchant registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'business_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'commercial_registration_number' => ['required', 'string', 'max:100'],
            'tax_number' => ['required', 'string', 'max:100'],
            'business_city' => ['required', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'merchant',
            'user_type' => 'merchant',
            'business_name' => $request->business_name,
            'phone' => $request->phone,
            'commercial_registration_number' => $request->commercial_registration_number,
            'tax_number' => $request->tax_number,
            'business_city' => $request->business_city,
            'merchant_status' => 'pending', // Default status for new merchants
        ]);

        event(new Registered($user));

        Auth::guard('merchant')->login($user);

        return redirect('/merchant/dashboard');
    }
}
